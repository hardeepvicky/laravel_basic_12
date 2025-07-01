<?php
namespace App\Helpers;

use App\Exceptions\UserException;
use App\Models\CommandLog;
use Illuminate\Console\Command;

class MyCommand extends Command
{
    public CommandLog $logModel;

    public function logCreate(array $save_data) : CommandLog
    {        
        $this->logModel = new CommandLog($save_data);

        return $this->logModel;
    }

    public function logUpdate($save_data)
    {
        $this->logModel->exec_time = $save["exec_time"] = microtime(true) - LARAVEL_START;
        $this->logModel->info = $save_data;

        if (!$this->logModel->saveQuietly()) {
            throw new UserException("Fail to Save Log", UserException::ERROR_LARAVEL_FAIL);
        }
        
        $db_logs = $this->getQueryLog();

        if ($db_logs)
        {
            $this->logModel->have_dml_query = $db_logs['have_dml_query'];
            $this->logModel->have_heavy_query = $db_logs['have_heavy_query'];

            if (!$this->logModel->saveQuietly()) {
                throw new UserException("Fail to Save Log", UserException::ERROR_LARAVEL_FAIL);
            }

            $base_file_name = date_get_for_file_name() . "_";

            $disk = $this->logModel->getStorageDisk();

            $fileUtility = new FileUtility($disk);

            $path = $this->logModel->getStoragePath();
            $filename = $base_file_name . "_sql.txt";
            $content = implode(PHP_EOL, $db_logs['sql_list']);

            $fileUtility->writeLog($path, $filename, $content);

            $this->logModel->sql_log_file = $path . $filename;

            if ($this->logModel->have_dml_query) {
                $filename = $base_file_name . "_sql_dml.txt";

                $content = implode(PHP_EOL, $db_logs['dml_sql_list']);

                $fileUtility->writeLog($path, $filename, $content);

                $this->logModel->sql_dml_log_file = $path . $filename;
            }

            if (!$this->logModel->saveQuietly()) {
                throw new UserException("Fail to Save Log", UserException::ERROR_LARAVEL_FAIL);
            }
        }
    }

    protected function getQueryLog()
    {   
        $db_logs = LaravelExtend::getQueryLog();

        $sql_list = $dml_sql_list = [
            implode(",", ["Query", "Time-In-MilliSeconds"])
        ];

        $ret_rows = [
            "have_dml_query" => false,
            "have_heavy_query" => false,
        ];

        foreach ($db_logs as $row) {
            $query = $row['query'];

            $sql_row = implode(", ", [$query, $row["time"]]);

            $sql_list[] = $sql_row;

            $query_first_10_chars = strtolower(substr($query, 0, 10));;

            $is_dml = str_check_char_array_exist($query_first_10_chars, ["insert", "update", "delete"]);

            if ($is_dml) {
                $dml_sql_list[] = $sql_row;
                $ret_rows['have_dml_query'] = true;
            }

            if ($row["time"] > 1000) {
                $ret_rows['have_heavy_query'] = true;
            }
        }

        $ret_rows['sql_list'] = $sql_list;
        $ret_rows['dml_sql_list'] = $dml_sql_list;

        return $ret_rows;
    }
}