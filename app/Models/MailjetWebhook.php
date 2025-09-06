<?php

namespace App\Models;

class MailjetWebhook extends BaseMongoModel
{
    public static function getStoragePath()
    {
        return "logs/MailjetWebhook/";
    }
}
