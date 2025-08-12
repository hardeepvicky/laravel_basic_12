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
        $str = preg_replace('/[^a-zA-Z0-9_|-]+/', '-', $key);

        return $str;
    }

    public static function getCache($key, array $tags = [])
    {
        $base_cache_key = static::getCacheKey();

        $cache_key = static::optimizeCacheKey($key);

        $tags[] = $base_cache_key;

        foreach($tags as $k => $tag)
        {
            $tags[$k] = static::optimizeCacheKey($tag);
        }

        $instance = Cache::tags($tags);

        return $instance->get($cache_key);
    }

    public static function addCache($key, $data, $cache_time_in_seconds = null, array $tags = [])
    {
        if (!$cache_time_in_seconds) {
            $cache_time_in_seconds = static::$cache_time_in_seconds;
        }

        $base_cache_key = static::getCacheKey();

        $tags[] = $base_cache_key;

        foreach($tags as $k => $tag)
        {
            $tags[$k] = static::optimizeCacheKey($tag);
        }

        $instance = Cache::tags($tags);

        $cache_key = static::optimizeCacheKey($key);

        //put cache
        if (!$instance->put($cache_key, $data, $cache_time_in_seconds)) {
            throw new UserException("Fail To Put Cache", UserException::ERROR_LARAVEL_FAIL);
        }

        Log::debug("Cache-put", [
            "tags" => $tags,
            "key" => $cache_key,
            "time" => $cache_time_in_seconds,
            "data" => $data
        ]);
    }

    public static function forgotCache($key = null, array $tags = [])
    {
        $base_cache_key = static::getCacheKey();

        if (empty($tags))
        {
            $tags = [$base_cache_key];
        }

        foreach($tags as $k => $tag)
        {
            $tags[$k] = static::optimizeCacheKey($tag);
        }

        $instance = Cache::tags($tags);

        if ($key)
        {
            $cache_key = static::optimizeCacheKey($key);
            $instance->forget($cache_key);

            Log::debug("Cache-forget", [
                "tags" => $tags,
                "key" => $cache_key,
            ]);
        }
        else
        {
            $instance->flush();
            Log::debug("Cache-flush", [
                "tags" => $tags,
            ]);
        }
    }
}
