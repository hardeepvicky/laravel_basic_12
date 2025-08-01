<?php

namespace App\Traits;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

        Log::debug("Cache-put", [
            "tags" => [$base_cache_key],
            "key" => $cache_key,
            "time" => $cache_time_in_seconds,
            "data" => $data
        ]);
    }

    public static function forgotCache($key = null)
    {
        $base_cache_key = static::getCacheKey();

        $instance = Cache::tags([$base_cache_key]);

        if ($key)
        {
            $cache_key = static::optimizeCacheKey($key);
            $instance->forget($cache_key);

            Log::debug("Cache-forget", [
                "tags" => [$base_cache_key],
                "key" => $cache_key,
            ]);
        }
        else
        {
            $instance->flush();
            Log::debug("Cache-flush", [
                "tags" => [$base_cache_key],
            ]);
        }
    }
}
