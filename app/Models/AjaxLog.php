<?php

namespace App\Models;

class AjaxLog extends BaseMongoModel
{
    protected $guarded = ["id"];

    public static function getStoragePath()
    {
        return "logs/ajax/";
    }

    public function cacheLog()
    {
        return $this->belongsTo(CacheLog::class);
    }

    public function sqlLog()
    {
        return $this->belongsTo(SqlLog::class);
    }
}
