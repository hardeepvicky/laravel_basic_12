<?php
namespace App\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait MysqlTableInfoTrait
{
    protected static $tableInfo = [];
    
    public static function getTableInfo($table_name)
    {
        $cache_key = "mysql-table-info";

        $table_info = self::getCache($cache_key);

        if ($table_info)
        {
            return $table_info;
        }

        $table_info = [
            "columns" => []
        ];

        $columns = DB::select('SHOW COLUMNS FROM ' . $table_name);

        foreach ($columns as $column) {
            $table_info['columns'][$column->Field] = (array) $column;
        }

        if (App::isLocal())
        {
            $cache_time = 300;
        }
        else
        {
            $cache_time = 60 * 60 * 24 * 30;
        }

        self::addCache($cache_key, $table_info, $cache_time);

        return $table_info;
    }

    public static function getTableColumnInfo($table_name, $field)
    {
        $info = self::getTableInfo($table_name);

        if (isset($info["columns"][$field])) {
            return $info["columns"][$field];
        }

        return [];
    }

    public static function tableHaveField($table_name, $field)
    {
        $info = self::getTableColumnInfo($table_name, $field);

        if (!empty($info)) {
            return true;
        }

        return false;
    }
}