<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('admin_users')) {
            Schema::create('admin_users', function (Blueprint $table) {
                $table->increments('uid');
                $table->string('username', 30)->unique();
                $table->string('password', 60);
                $table->rememberToken();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('admin_users_permissions')) {
            Schema::create('admin_users_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uid')->unsigned();
                $table->string('route_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('admin_users');
        Schema::dropIfExists('admin_users_permissions');
    }
}
