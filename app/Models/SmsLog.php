<?php

namespace App\Models;

class SmsLog extends BaseMongoModel
{
    protected $guarded = ["id"];

    public static function getStoragePath()
    {
        return "logs/sms/";
    }
}
