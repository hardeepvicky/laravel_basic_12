<?php
namespace App\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

trait ModelListTrait
{
    public static function getList(String $key_field = "id", String $value_field = "display_name", array $conditions = [], $order_by = "name", $order_dir = "asc")
    {
        $builder = static::query();

        $model = new static();
        $table_name = $model->getTable();

        $cache_key = "list-" . $key_field . "-" . $value_field . "-";

        foreach($conditions as $k => $v)
        {
            if (is_array($v))
            {
                $v = implode(",", array_get_scaler_value_only($v));

                if (strlen($v) > 50)
                {
                    $v = substr($v, 0, 50);
                }
            }

            $cache_key .= Str::snake($k) . ":" . Str::snake($v);
        }

        if (strlen($cache_key) > 255)
        {
            $cache_key = substr($cache_key, 0, 255);
        }

        $list = [];

        if (!App::isLocal())
        {
            $list = static::getCache($cache_key);
        }

        if (!$list)
        {
            $builder->orWhere(function ($query) use ($conditions) {
                if (isset($conditions['or_id'])) {
                    $v = $conditions['or_id'];

                    if (is_array($v)) {
                        if (!empty($v)) {
                            $query->whereIn("id", $v);
                        }
                    } else {
                        $query->where("id", $v);
                    }
                }
            });

            unset($conditions['or_id']);

            if (static::tableHaveField($table_name, 'is_active')) {
                if (!isset($conditions['is_active'])) {
                    $conditions['is_active'] = 1;
                }
            }

            $builder->orWhere(function ($query) use ($conditions) {
                foreach ($conditions as $k => $value) {
                    if (is_array($value)) {
                        if (!empty($value)) {
                            $query->whereIn($k, $value);
                        }
                    } else {
                        $query->where($k, $value);
                    }
                }
            });

            if ($order_by && $order_dir) {
                $builder->orderBy($order_by, $order_dir);
            }

            $list = static::fetchList($builder, $key_field, $value_field);

            if (!App::isLocal())
            {
                $cache_time = 60 * 60;

                self::addCache($cache_key, $list, $cache_time);
            }
        }

        return $list;
    }

    public static function getListCache(String $key_field = "id", String $value_field = "display_name", $order_by = "name", $order_dir = "ASC")
    {
        $cache_key = "list-" . $key_field . "-" . $value_field;

        $list = static::getCache($cache_key);

        if ($list) {
            return $list;
        }

        $builder = static::query();

        if ($order_by && $order_dir) {
            $builder->orderBy($order_by, $order_dir);
        }

        $list = static::fetchList($builder, $key_field, $value_field);

        $cache_time = 60 * 60 * 24 * 30;

        self::addCache($cache_key, $list, $cache_time);

        return $list;
    }
}