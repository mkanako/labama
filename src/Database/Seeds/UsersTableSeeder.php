<?php

namespace Cc\Labama\Database\Seeds;

use Cc\Labama\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (0 == User::count()) {
            User::create([
                'username' => 'admin',
                'password' => Hash::make('admin'),
            ]);
        }
    }
}
