<?php

namespace App\Models;

class PermissionChangeLog extends BaseMongoModel
{
    public static function getStoragePath()
    {
        return "logs/PermissionChangeLog/";
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
