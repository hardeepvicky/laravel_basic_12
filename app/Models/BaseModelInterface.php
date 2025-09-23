<?php

namespace App\Models;

interface BaseModelInterface
{
    public static function classDisplayName(): string;

    public static function getCacheKey(): string;

    public static function getCache($key);

    public static function addCache($key, $data);

    public static function getStorageDisk();

    public static function getStoragePath();

    public static function forgotCache($key);

    public static function insertOrUpdate(array $data, &$is_insert = null, &$is_update = null, bool $save_quitely = false);

    public static function insertIgnoreIfExist(array $data, &$is_insert = null, bool $save_quitely = false);

    public static function getUniqueRecord(array $data);

    public function getNextId();

    public function getClassDisplayName();

    public function beforeSave();

    public function beforeDelete();

    public function afterFind();
}
