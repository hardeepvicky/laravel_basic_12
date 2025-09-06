<?php

namespace App\Models;

class ImportLog extends BaseMongoModel
{
    protected static array $file_fields = ["file"];

    public static function getStoragePath()
    {
        return "logs/ImportLog";
    }

    public function sqlLog()
    {
        return $this->belongsTo(SqlLog::class);
    }
}
