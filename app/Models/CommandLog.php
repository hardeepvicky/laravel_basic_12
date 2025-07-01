<?php

namespace App\Models;

class CommandLog extends BaseMongoModel
{
    protected static array $file_fields = ["sql_log_file", "sql_dml_log_file"];

    public static function getStoragePath()
    {
        return "logs/command/";
    }
}
