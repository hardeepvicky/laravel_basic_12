<?php

namespace App\Models;

class SqlLog extends BaseMongoModel
{
    protected static array $file_fields = ["sql_log_file", "sql_dml_log_file"];

    public static function getStoragePath()
    {
        return "logs/sql/";
    }
}
