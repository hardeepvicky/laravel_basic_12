<?php

namespace App\Models;

use App\Helpers\FileUtility;

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

        $fileUtility = new FileUtility(static::getStorageDisk());

        if ($this->content_file) {
            $this->content_file_url = $fileUtility->getUrl($this->content_file);
        }
    }
}
