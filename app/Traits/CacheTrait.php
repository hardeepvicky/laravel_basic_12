<?php

namespace App\Traits;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    public static int $cache_time_in_seconds = 60;
    public static array $cache_logs = [];

    public static function optimizeCacheKey($key): string
    {
        $str = preg_replace('/[^a-zA-Z0-9]+/', '-', $key);

        return $str;
    }

    public static function getCache($key)
    {
        $base_cache_key = static::getCacheKey();

        $cache_key = static::optimizeCacheKey($key);

        $instance = Cache::tags([$base_cache_key]);

        return $instance->get($cache_key);
    }

    public static function addCache($key, $data, $cache_time_in_seconds = null)
    {
        if (!$cache_time_in_seconds) {
            $cache_time_in_seconds = static::$cache_time_in_seconds;
        }

        $base_cache_key = static::getCacheKey();

        $instance = Cache::tags([$base_cache_key]);

        $cache_key = static::optimizeCacheKey($key);

        //put cache
        if (!$instance->put($cache_key, $data, $cache_time_in_seconds)) {
            throw new UserException("Fail To Put Cache", UserException::ERROR_LARAVEL_FAIL);
        }
    }

    public static function forgotCache($key = null)
    {
        $base_cache_key = static::getCacheKey();

        $instance = Cache::tags([$base_cache_key]);

        $cache_key = static::optimizeCacheKey($key);

        return $instance->get($cache_key);
    }
}
