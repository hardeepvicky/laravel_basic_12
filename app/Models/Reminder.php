<?php

namespace App\Models;

class Reminder extends BaseMongoModel
{
    protected static array $unique_fields = ["from_date", "to_date", "msg"];

    public static function getStoragePath()
    {
        return "logs/reminder/";
    }
}
