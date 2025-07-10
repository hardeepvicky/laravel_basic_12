<?php

namespace App\Helpers;

use App\Helpers\ConsoleOutput;
use Exception;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MyMigration extends Migration
{
    private $query_list = [];

    private static $output;

    public static function getConsoleOutput()
    {
        if (is_null(self::$output))
        {
            self::$output = new ConsoleOutput();
        }

        return self::$output;
    }

    public function addQueryToList(string | array $data)
    {
        if (!is_array($data)) {
            $data = [$data];
        }

        if (is_array($data)) {
            foreach ($data as $q) {
                if (is_string($q)) {
                    $this->_addQueryToList($q);
                }
            }
        } else {
            $this->_addQueryToList($data);
        }
    }

    private function _addQueryToList(string $q)
    {
        $q = trim($q);

        if ($q) {
            $this->query_list[] = $q;
        }
    }

    public function clearQueryList()
    {
        $this->query_list = [];
    }

    public function runQueryList()
    {
        $output = self::getConsoleOutput();

        $output->clearLogs();

        $output->writeLogToConsole('Total SQL : ' . count($this->query_list));

        foreach ($this->query_list as $index => $q) {
            $output->clearLogs();

            $str_sub = substr($q, 0, 20) . "....";

            $output->writeLogToConsole('Running SQL : ' . ($index + 1) . " : " . $str_sub);

            DB::statement($q);
        }
    }

    public function getColumns(string $tableName)
    {
        $output = self::getConsoleOutput();

        $output->writeLogToConsole('Fetching Columns');

        if (!Schema::hasTable($tableName)) {
            throw new Exception("$tableName not found in database");
        }

        $columns = Schema::getColumnListing($tableName);

        return $columns;
    }

    public function getForeignKeys(string $tableName)
    {
        $output = self::getConsoleOutput();

        $output->writeLogToConsole('Fetching Foreign keys');

        $databaseName = DB::connection()->getDatabaseName();

        $foreignKeys = DB::select("
            SELECT
                kcu.COLUMN_NAME AS foreign_key_column,
                kcu.REFERENCED_TABLE_NAME AS referenced_table,
                kcu.REFERENCED_COLUMN_NAME AS referenced_column,
                kcu.CONSTRAINT_NAME AS constraint_name
            FROM
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS kcu
            WHERE
                kcu.TABLE_SCHEMA = ? AND
                kcu.TABLE_NAME = ? AND
                kcu.REFERENCED_TABLE_NAME IS NOT NULL;
        ", [$databaseName, $tableName]);

        return $foreignKeys;
    }

    public function getIndexKeys(string $tableName)
    {
        $output = self::getConsoleOutput();

        $output->writeLogToConsole('Fetching Index keys');

        $databaseName = DB::connection()->getDatabaseName();
        $result = DB::table('information_schema.statistics')
            ->where('table_schema', $databaseName)
            ->where('table_name', $tableName)
            ->get();

        return $result;
    }

    public function indexExists($tableName, $index)
    {
        $output = self::getConsoleOutput();

        $output->writeLogToConsole('Fetching Index');

        $databaseName = DB::connection()->getDatabaseName();
        $result = DB::table('information_schema.statistics')
            ->where('table_schema', $databaseName)
            ->where('table_name', $tableName)
            ->where('index_name', $index)
            ->exists();

        return $result;
    }

    public function dropColumnsWithIndex(string $tableName, array $fields)
    {
        $output = self::getConsoleOutput();

        $columns = $this->getColumns($tableName);

        foreach($fields as $k => $field)
        {
            if (!in_array($field, $columns))
            {
                $output->writeLogToConsole("Field not found : $field");
                unset($fields[$k]);
            }
        }

        $foreignKeys = $this->getForeignKeys($tableName);

        $output->writeLogToConsole('Droping foreign Keys');

        foreach($foreignKeys as $foreign_key)
        {
            if (in_array($foreign_key->foreign_key_column, $fields))
            {
                $output->writeLogToConsole('Droping foreign key : ' .$foreign_key->constraint_name);

                Schema::table($tableName, function (Blueprint $table) use($foreign_key) {                
                    $table->dropForeign($foreign_key->constraint_name);
                });
            }
        }

        $this->dropIndexIfExist($tableName, $fields);

        $output = self::getConsoleOutput();

        $output->writeLogToConsole('Droping fields : ' . implode(",", $fields));

        if ($fields)
        {
            Schema::dropColumns($tableName, $fields);
        }
    }

    public function dropIndexIfExist(string $tableName, array $fields)
    {
        $output = self::getConsoleOutput();

        $indexKeys = $this->getIndexKeys($tableName);

        $output->writeLogToConsole('Droping index Keys');

        foreach($indexKeys as $index_key)
        {
            if (in_array($index_key->COLUMN_NAME, $fields))
            {
                if ($this->indexExists($tableName, $index_key->INDEX_NAME))
                {
                    $output->writeLogToConsole('Droping index key : ' . $index_key->INDEX_NAME);

                    Schema::table($tableName, function (Blueprint $table) use($index_key) {                
                        $table->dropIndex($index_key->INDEX_NAME);
                    });
                }
            }
        }
    }
}
