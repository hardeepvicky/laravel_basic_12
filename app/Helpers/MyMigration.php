<?php

namespace App\Helpers;

use App\Helpers\ConsoleOutput;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MyMigration extends Migration
{
    private $query_list = [];

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
        $output = new ConsoleOutput();

        $output->clearLogs();

        $output->addLogMessage('Total SQL : ' . count($this->query_list));

        $output->writeLogToConsole();

        foreach ($this->query_list as $index => $q) {
            $output->clearLogs();

            $str_sub = substr($q, 0, 20) . "....";

            $output->addLogMessage('Running SQL : ' . ($index + 1) . " : " . $str_sub);

            $output->writeLogToConsole();

            DB::statement($q);
        }
    }

    public function indexExists($table, $index)
    {
        $database = DB::getDatabaseName();
        $result = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();

        return $result;
    }
}
