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

        $cache_key = $base_cache_key . "-" . static::optimizeCacheKey($key);

        $result = null;

        if (Cache::has($cache_key)) {
            $result = Cache::get($cache_key);
            self::addLog($base_cache_key, "get", $cache_key, $result);
        }
        else
        {
            self::addLog($base_cache_key, "not-found", $cache_key);
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
        else
        {
            self::addLog($base_cache_key, "put", $cache_key, $data);
        }

        //make refrence to cache keys 
        $list_of_cache_keys = [];
        if (Cache::has($base_cache_key)) {
            $list_of_cache_keys = Cache::get($base_cache_key);
            self::addLog($base_cache_key, "get", $base_cache_key, $list_of_cache_keys);
        }
        else
        {
            self::addLog($base_cache_key, "not-found", $base_cache_key);
        }

        if (!in_array($cache_key, $list_of_cache_keys)) {
            $list_of_cache_keys[] = $cache_key;
        }

        if (!Cache::put($base_cache_key, $list_of_cache_keys, $cache_time_in_seconds)) {
            throw new UserException("Fail To Put Cache", UserException::ERROR_LARAVEL_FAIL);
        }
        else
        {
            self::addLog($base_cache_key, "put", $base_cache_key, $list_of_cache_keys);
        }
    }

    public static function forgotCache($key = null)
    {
        $base_cache_key = static::getCacheKey();

        if (Cache::has($base_cache_key)) {
            $list_of_cache_keys = Cache::get($base_cache_key);

            self::addLog($base_cache_key, "get", $base_cache_key, $list_of_cache_keys);

            foreach ($list_of_cache_keys as $cache_key) {
                if (is_null($key) || $cache_key == $key)
                {
                    if (Cache::has($cache_key))
                    {
                        if (!Cache::forget($cache_key))
                        {
                            throw new UserException("Fail To Forgot Cache", UserException::ERROR_LARAVEL_FAIL);
                        }
                        else
                        {
                            self::addLog($base_cache_key, "forget", $cache_key);
                        }
                    }
                    else
                    {
                        self::addLog($base_cache_key, "not-found", $cache_key);
                    }
                }
            }
        }
        else
        {
            self::addLog($base_cache_key, "not-found", $base_cache_key);
        }
    }

    protected static function addLog($base_cache_key, string $op, $cache_key, $data = null)
    {
        $cache_log_level = env("APP_CACHE_LOG_LEVEL", 0);

        if ($cache_log_level == 0)
        {
            return;
        }

        if ($cache_log_level < 2)
        {
            $data = null;
        }
        else if ($cache_log_level < 3)
        {
            if (is_array($data))
            {
                $data = array_keys($data);
            }
        }
        else if ($cache_log_level < 4)
        {
            if (is_array($data) && count($data) > 0)
            {
                $data = array_purge_if_exceed_limit($data, 20, true);
            }
        }

        $call_by_list = [];

        if ($cache_log_level >= 5)
        {
            foreach(debug_backtrace() as $callBy)
            {
                if (count($call_by_list) < 5)
                {
                    $call_by_list[] = [
                        "file" => $callBy['file'],
                        "function" => $callBy['function'],
                        "line" => $callBy['line'],
                    ];
                }
            }
        }

        self::$cache_logs[$base_cache_key][] = [
            "op" => $op,
            "cache_key" => $cache_key,
            "data" => $data,
            "call_from" => $call_by_list,
        ];
    }
}
