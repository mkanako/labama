<?php

namespace Cc\Labama\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'labama:install {--force}';

    protected $description = 'Install Command';

    public function handle()
    {
        $this->call('vendor:publish', ['--provider' => 'Cc\Labama\AdminServiceProvider', '--force' => $this->option('force')]);
        $this->initDatabase();
        $this->initDirectory();
    }

    public function initDatabase()
    {
        $this->call('migrate');
        $this->call('db:seed', ['--class' => \Cc\Labama\Database\Seeds\AdminTablesSeeder::class]);
    }

    public function initDirectory()
    {
        $directory = app_path('Admin');
        if (is_dir($directory)) {
            $this->line("<error>{$directory} directory already exists !</error> ");
            return;
        }
        mkdir($directory, 0755, true);
        mkdir($directory . '/Controllers', 0755, true);
        $this->line('<info>Admin directory was created:</info> ' . str_replace(base_path(), '', $directory));
        $data = ['namespace' => 'App\Admin\Controllers'];
        $this->compileStub('routes', $directory, $data);
        $this->compileStub('HomeController', $directory . '/Controllers', $data);
    }

    public function compileStub($name, $dest, $data = [])
    {
        $content = file_get_contents(__DIR__ . '/stubs/' . $name . '.stub');
        foreach ($data as $key => $value) {
            $content = str_replace(
                '{{' . $key . '}}',
                $value,
                $content
            );
        }
        file_put_contents($dest . '/' . $name . '.php', $content);
    }
}
