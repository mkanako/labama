<?php

namespace Cc\Labama\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use  Notifiable;
    protected $table = LABAMA_ENTRY . '_users';
    protected $primaryKey = 'uid';

    protected $fillable = [
        'username', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
