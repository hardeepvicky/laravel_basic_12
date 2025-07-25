<?php

namespace App\Models;

class WhatsappLog extends BaseMongoModel
{
    protected $guarded = ["id"];

    public static function getStoragePath()
    {
        return "logs/whatsapp/";
    }
}
