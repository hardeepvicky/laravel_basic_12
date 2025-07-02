<?php

namespace App\Models;

class CacheLog extends BaseMongoModel
{
    public static function getStoragePath()
    {
        return "logs/CacheLog/";
    }
}
