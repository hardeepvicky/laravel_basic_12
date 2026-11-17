<?php

namespace App\Models;

use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use Illuminate\Database\Eloquent\Builder;

class SqlLog extends BaseMongoModel
{
    protected static array $select_queries = [];

    public static function getStoragePath()
    {
        return "logs/sql/";
    }

    public function afterFind()
    {
        parent::afterFind();

        $fileUtility = new FileUtility(static::getStorageDisk());

        $files = [];
        
        foreach ($this->files as $k => $file_arr) {
            if ($file_arr['file']) {
                $file_arr['file_url'] = $fileUtility->getUrl($file_arr['file']);
            }

            $files[$k] = $file_arr;
        }

        $this->files = $files;
    }

    public static function addSelectQuery(Builder $qb, string $connection = "")
    {
        $sql = $qb->toSql();

        $bindings = $qb->getBindings();

        $fullSql = vsprintf(str_replace('?', "'%s'", $sql), $bindings);

        $count = $qb->count();

        if (!$connection)
        {
            $connection = config('database.default');
        }

        self::$select_queries[$connection][] = [
            "sql" => $fullSql,
            "count" => $count,
        ];
    }

    public static function fetchSqlLog()
    {
        $all_logs = [];

        $default_data = [
            "have_dml_query" => false,
            "have_heavy_query" => false,
            "sql_list" => [],
            "dml_sql_list" => [],
            "heavy_sql_list" => [],
            "select_sql_list" => [],
        ];

        $db_config_list = config("database.connections");

        $mysql_connection_list = [];

        foreach($db_config_list as $db_config_key => $db_config)
        {
            if ($db_config['driver'] == "mysql")
            {
                $mysql_connection_list[$db_config_key] = $db_config;
            }
        }

        foreach($mysql_connection_list as $db_config_key => $db_config)
        {
            $db_logs = LaravelExtend::getQueryLog($db_config_key);

            $record = $default_data;

            foreach ($db_logs as $row) {
                $query = $row['query'];

                $record['sql_list'][] = $query;

                $query_first_10_chars = strtolower(substr($query, 0, 10));;

                $is_dml = str_check_char_array_exist($query_first_10_chars, ["insert", "update", "delete"]);

                if ($is_dml) {
                    $record['dml_sql_list'][] = $query;
                    $record['have_dml_query'] = true;
                }

                if ($row["time"] > 1000) {

                    $record['heavy_sql_list'][] = [
                        "query" => $query,
                        "time_in_milli_seconds" => $row['time']
                    ];

                    $record['have_heavy_query'] = true;
                }
            }

            $record['select_sql_list'] = self::$select_queries[$db_config_key] ?? [];

            $all_logs[$db_config_key] = $record;
        }

        return $all_logs;
    }
}
