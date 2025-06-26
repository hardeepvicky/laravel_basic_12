<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;

trait CacheTrait
{

    public static $model_cache_key;

    public static function getModelCacheKey(): string
    {
        $str = get_called_class();
        $arr = explode("\\", $str);
        $str = end($arr);
        $str = trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $str));

        $key = get_cache_prefix() . "-" . $str;

        return  static::optimizeCacheKey($key);
    }

    public static function optimizeCacheKey($key): string
    {
        $key = str_replace(".", "-", $key);

        $key = preg_replace('!\s+!', '-', $key);

        $key = trim($key, "-");

        $key = trim($key);

        return $key;
    }

    public static function getCache($key)
    {
        $model_cache_key = static::getModelCacheKey();

        $cache_key = $model_cache_key . "-" . $key;

        if (Cache::has($cache_key)) {
            return Cache::get($cache_key);
        }

        return null;
    }

    public static function addCache($key, $data)
    {
        $model_cache_key = static::getModelCacheKey();

        $cache_key = $model_cache_key . "-" . static::optimizeCacheKey($key);

        //put cache
        Cache::put($cache_key, $data, laravel_constant("cache_time.model"));

        //make refrence to cache keys 
        $list_of_cache_keys = [];
        if (Cache::has($model_cache_key)) {
            $list_of_cache_keys = Cache::get($list_of_cache_keys);
        }

        if (!in_array($cache_key, $list_of_cache_keys)) {
            $list_of_cache_keys[] = $cache_key;
        }

        if (!Cache::put($model_cache_key, $list_of_cache_keys, laravel_constant("cache_time.model"))) {
            throw_exception("Fail To Put Cache");
        }
    }

    public function forgotCache()
    {
        $model_cache_key = static::getModelCacheKey();

        if (Cache::has($model_cache_key)) {
            $list_of_cache_keys = Cache::get($model_cache_key);

            foreach ($list_of_cache_keys as $cache_key) {
                Cache::forget($cache_key);
            }

            Cache::forget($model_cache_key);
        }
    }
}
