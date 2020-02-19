<?php

namespace Cc\Labama\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = 'labama:install {--dir=Admin} {--force}';
    protected $description = 'Install Command';

    public function handle()
    {
        list('dir' => $dir, 'force' => $force) = $this->options();
        $prefix = strtolower($dir);
        $dir = $this->dir = app_path(ucfirst($dir));
        $this->force = $force;
        define('LABAMA_ENTRY', $prefix);

        if (is_dir($dir) && !$force) {
            $this->error("`{$dir}` already exists!");
            return;
        }

        $config = config('labama', []);
        $config[$prefix] = require __DIR__ . '/../config.php';
        if (!file_exists(config_path('labama.php')) || $force) {
            false !== file_put_contents(config_path('labama.php'), "<?php\n\nreturn " . var2export($config, true) . ";\n") && $this->info('created: config/labama.php');
        }

        $this->createDir();
        $this->createDir('Controllers');
        $this->createDir('Models');
        $this->createDir('Middleware');

        $data = ['prefix' => $prefix];

        $this->compileStub('HomeController', $dir . '/Controllers', $data);
        $this->compileStub('routes', $dir, $data);

        $migration_file = Arr::first(scandir(database_path('migrations')), function ($value, $key) use ($prefix) {
            return Str::endsWith($value, 'create_' . $prefix . '_users_table.php');
        }, false) ?: (date('Y_m_d_His') . '_create_' . $prefix . '_users_table.php');
        if (true === $this->compileStub('create_users_table', database_path('migrations') . '/' . $migration_file, $data)) {
            $this->call('migrate');
            $this->call('db:seed', ['--class' => \Cc\Labama\Database\Seeds\UsersTableSeeder::class]);
        }
        $this->modifyExceptions();

        $this->call('attacent:install', ['--force' => $this->option('force')]);

        $this->info('install complete!');
    }

    private function createDir($name = '')
    {
        $path = join('/', [$this->dir, trim($name, '/')]);
        !is_dir($path) && mkdir($path, 0755, true) && $this->info('directory created: ' . $path);
    }

    private function compileStub($stub, $dest, $data = [])
    {
        if (!preg_match('/.+\/.+\..+/', $dest)) {
            $dest .= '/' . $stub . '.php';
        }
        if (!file_exists($dest) || !empty($this->force)) {
            $content = file_get_contents(__DIR__ . '/stubs/' . $stub . '.stub');
            foreach ($data as $key => $value) {
                $content = str_replace(
                    '{{' . $key . '}}',
                    $value,
                    $content
                );
                $content = str_replace(
                    '{{' . ucfirst($key) . '}}',
                    ucfirst($value),
                    $content
                );
            }
            if (false !== file_put_contents($dest, $content)) {
                $this->info('created: ' . str_replace(base_path() . '/', '', $dest));
                return true;
            }
        }
        return false;
    }

    private function modifyExceptions()
    {
        $namespace_code = <<<EOT
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
EOT;
        $render_code = <<<'EOT'
        if (in_array(current(explode('/', trim($request->getPathInfo(), '/'))), array_keys(config('labama')))) {
            if ($exception instanceof MethodNotAllowedHttpException ||  $exception instanceof NotFoundHttpException) {
                return err('Not Found');
            }
        }
EOT;
        $path = app_path() . '/Exceptions/Handler.php';
        $handler_file = file_get_contents($path);
        preg_match_all('/use.+?;/', $handler_file, $matches);
        if (!empty($matches[0])) {
            $first = Arr::first($matches[0], function ($value) {
                return  preg_match('/use\s+?Symfony.+?Exception.+?;/', $value);
            }, false);
            if (false === $first) {
                $last = Arr::last($matches[0]);
                $handler_file = str_replace($last, $last . "\n" . $namespace_code, $handler_file);
                $handler_file = preg_replace('/(public\s+?function\s+?render.+?\n?.*?{)/', "$1\n$render_code", $handler_file);
                file_put_contents($path, $handler_file);
            }
        }
    }
}

function var2export($expression, $return = false)
{
    $export = var_export($expression, true);
    $export = preg_replace('/^([ ]*)(.*)/m', '$1$1$2', $export);
    $array = preg_split("/\r\n|\n|\r/", $export);
    $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $array);
    $export = join(PHP_EOL, array_filter(['['] + $array));
    if ((bool) $return) {
        return $export;
    }
    echo $export;
}
