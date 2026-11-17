<?php

namespace App\Models;

class AjaxLog extends BaseMongoModel
{
    public static function getStoragePath()
    {
        return "logs/ajax/";
    }

    public function sqlLog()
    {
        return $this->belongsTo(SqlLog::class);
    }
}
