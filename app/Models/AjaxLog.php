<?php

namespace App\Models;

class AjaxLog extends BaseMongoModel
{
    protected $guarded = ["id"];

    public static function getStoragePath()
    {
        return "logs/ajax/";
    }
}
