<?php

namespace App\Models;

class ImportLog extends BaseMongoModel
{
    public static function getStoragePath()
    {
        return "logs/ImportLog";
    }

    public function sqlLog()
    {
        return $this->belongsTo(SqlLog::class);
    }
}
