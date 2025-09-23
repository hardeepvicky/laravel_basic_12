<?php

namespace App\Helpers;

use App\Exceptions\UserException;
use App\Models\CommandLog;
use App\Models\SqlLog;
use Illuminate\Console\Command;

class MyCommand extends Command
{
    private CommandLog $logModel;

    public array $output_lines = [];

    public function logCreate(array $save_data): CommandLog
    {
        $this->logModel = new CommandLog($save_data);

        return $this->logModel;
    }

    public function logUpdate($save_data)
    {
        $this->logModel->exec_time = microtime(true) - LARAVEL_START;
        $this->logModel->info = $save_data;
        $this->logModel->output = $this->output_lines;

        $sqlLogModel = LaravelExtend::saveSqlLog([
            "command_log_id" => $this->logModel->id
        ]);

        $this->logModel->sql_log_id = $sqlLogModel->id;

        if (!$this->logModel->saveQuietly()) {
            throw new UserException("Fail to Save Log", UserException::ERROR_LARAVEL_FAIL);
        }
    }

    public function line($string, $style = null, $verbosity = null)
    {
        $new_str = $style ? $style . " : " : $style;

        $this->output_lines[] = $new_str . $string;
        
        parent::line($string, $style, $verbosity);
    }
}
