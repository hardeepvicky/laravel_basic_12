<?php

namespace App\Models;

class EmailLog extends BaseMongoModel
{
    protected $connection = 'mongodb';

    protected $guarded = ["id"];

    public static function getFileSavePath()
    {
        return "files/" . str_class_name_without_namespace(__CLASS__) . "/";
    }
}
