<?php

namespace Cc\Labama\Database\Seeds;

use Cc\Labama\Models\AdminUser as User;
use Illuminate\Database\Seeder;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (0 == User::count()) {
            User::create([
                'username' => 'admin',
                'password' => bcrypt('admin'),
            ]);
        }
    }
}
