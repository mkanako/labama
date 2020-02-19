<?php

namespace Cc\Labama\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $table = LABAMA_ENTRY . '_users_permissions';
    public $timestamps = false;
}
