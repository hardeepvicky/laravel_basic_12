<?php

namespace App\Models;

interface BaseModelInterface
{
    public static function classDisplayName(): string;

    public static function getModelCacheKey(): string;

    public static function getCache($key);

    public static function addCache($key, $data);

    public static function getStorageDisk();

    public function forgotCache();

    public function getNextId();

    public function getClassDisplayName();

    public function insertOrUpdate(array $data, &$is_insert = null, &$is_update = null);

    public function insertIgnoreIfExist(array $data);

    public function getUniqueId(array $data);
}
