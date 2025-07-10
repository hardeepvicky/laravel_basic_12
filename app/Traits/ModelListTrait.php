<?php
namespace App\Traits;

trait ModelListTrait
{
    public static function getList(String $key_field = "id", String $value_field = "display_name", $conditions = [], $order_by = "name", $order_dir = "asc")
    {
        $builder = static::query();

        $model = new static();
        $table_name = $model->getTable();

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

        return $list;
    }

    public static function getListCache(String $key_field = "id", String $value_field = "display_name", $order_by = "name", $order_dir = "ASC")
    {
        $key = "list-" . $key_field . "-" . $value_field;

        $list = static::getCache($key);

        if ($list) {
            return $list;
        }

        $builder = static::query();

        if ($order_by && $order_dir) {
            $builder->orderBy($order_by, $order_dir);
        }

        $list = static::fetchList($builder, $key_field, $value_field);

        self::addCache($key, $list);

        return $list;
    }
}