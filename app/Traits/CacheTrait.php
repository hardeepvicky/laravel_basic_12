<?php

namespace App\Traits;

use App\Exceptions\UserException;
use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    public static int $cache_time_in_seconds = 60;

    public static function getCacheKey(): string
    {
        $str = get_called_class();

        return static::optimizeCacheKey($str);
    }

    public static function optimizeCacheKey($key): string
    {
        $str = preg_replace('/[^a-zA-Z0-9]+/', '-', $key);

        return $str;
    }

    public static function getCache($key)
    {
        $base_cache_key = static::getCacheKey();

        $cache_key = $base_cache_key . "-" . static::optimizeCacheKey($key);

        $result = null;

        if (Cache::has($cache_key)) {
            $result = Cache::get($cache_key);
        }

        return $result;
    }

    public static function addCache($key, $data, $cache_time_in_seconds = null)
    {
        if (!$cache_time_in_seconds)
        {
            $cache_time_in_seconds = static::$cache_time_in_seconds;
        }

        $base_cache_key = static::getCacheKey();

        $cache_key = $base_cache_key . "-" . static::optimizeCacheKey($key);

        //put cache
        if (!Cache::put($cache_key, $data, $cache_time_in_seconds))
        {
            throw new UserException("Fail To Put Cache", UserException::ERROR_LARAVEL_FAIL);
        }

        //make refrence to cache keys 
        $list_of_cache_keys = [];
        if (Cache::has($base_cache_key)) {
            $list_of_cache_keys = Cache::get($list_of_cache_keys);
        }

        if (!in_array($cache_key, $list_of_cache_keys)) {
            $list_of_cache_keys[] = $cache_key;
        }

        if (!Cache::put($base_cache_key, $list_of_cache_keys, $cache_time_in_seconds)) {
            throw new UserException("Fail To Put Cache", UserException::ERROR_LARAVEL_FAIL);
        }
    }

    public static function forgotCache($key = null)
    {
        $base_cache_key = static::getCacheKey();

        if (Cache::has($base_cache_key)) {
            $list_of_cache_keys = Cache::get($base_cache_key);

            foreach ($list_of_cache_keys as $cache_key) {
                if (is_null($cache_key) || $cache_key == $key)
                {
                    if (!Cache::forget($cache_key))
                    {
                        throw new UserException("Fail To Forgot Cache", UserException::ERROR_LARAVEL_FAIL);
                    }
                }
            }
        }
    }
}
