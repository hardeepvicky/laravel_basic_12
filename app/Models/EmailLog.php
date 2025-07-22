<?php

namespace App\Models;

class EmailLog extends BaseMongoModel
{
    protected $guarded = ["id"];

    public static function getStoragePath()
    {
        return "logs/email/";
    }

    public function afterFind()
    {
        parent::afterFind();
        
        $this->convertTimeZoneForDisplay();
    }
}
