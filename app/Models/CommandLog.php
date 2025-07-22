<?php

namespace App\Models;

class CommandLog extends BaseMongoModel
{
    public static function getStoragePath()
    {
        return "logs/command/";
    }

    public function sqlLog()
    {
        return $this->belongsTo(SqlLog::class);
    }
}
