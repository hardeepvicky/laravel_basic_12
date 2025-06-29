<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait StorageTrait
{
    public static function getStorageDisk()
    {
        return Storage::disk(DEFAULT_DISK);
    }

    public static function getStoragePath()
    {
        return "user_files/" . str_class_name_without_namespace(get_called_class()) . "/";
    }

    public function getStoragePathForRecord()
    {
        return self::getStoragePath() . $this->id . "/";
    }
}
