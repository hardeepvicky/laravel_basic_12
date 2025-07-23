<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleOutput;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\MyCommand;
use App\Models\AjaxLog;
use App\Models\AutomateTask;
use App\Models\CommandLog;
use App\Models\DocumentFile;
use App\Models\EmailLog;
use App\Models\RecordHistory;
use App\Models\RecurringTask;
use App\Models\SqlLog;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\Voice\Record;

class LogsOldCommand extends MyCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for delete old logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info($this->description . " : " . now());

        $logModel = $this->logCreate([
            "command" => $this->signature,
            "description" => $this->description
        ]);

        $logModel->save();

        try {
            $now = DateUtility::getDate(null, DateUtility::DATETIME_FORMAT);

            $_7_days_before = DateUtility::change($now, -7, DateUtility::DAYS);

            $_90_days_before = DateUtility::change($now, -90, DateUtility::DAYS);

            $models = [
                AjaxLog::class => $_7_days_before,
                CommandLog::class => $_7_days_before,
                EmailLog::class => $_7_days_before,
                SqlLog::class => $_7_days_before,
                RecordHistory::class => $_90_days_before
            ];

            $log_info = [];

            foreach($models as $model => $days_before)
            {
                $counters = [
                    "found" => 0,
                    "insert" => 0,
                    "update" => 0,
                    "delete" => 0,
                ];
                
                $qb = $model::where("created_at", "<", $days_before);
                $qb->select(["id"]);
                $records = $qb->get();

                $counters['found'] += $records->count();

                // dd($records->toArray());

                if ($records && $records->count() > 0)
                {
                    foreach($records as $record)
                    {
                        if ($record->delete())
                        {
                            $counters['delete'] += 1;
                        }
                    }
                }

                $output = new ConsoleOutput();

                $counts = array_human_keys($counters);

                $model_class_name = str_class_name_without_namespace($model);

                $output->addLog($counts, 0, $model_class_name);

                $output->writeLogToConsole();

                $log_info[$model_class_name] = $counts;
            }

            $msg = $this->description . " : Completed successfully!";
            $this->info($msg);

            $this->logUpdate($log_info);

        } catch (\Exception $e) {
            $msg = $e->getMessage();

            $this->logUpdate([
                "error" => $msg
            ]);

            $this->error($msg);
            
            Log::error($msg, ['exception' => $e]);
        }

        return Command::SUCCESS; // Indicate success
    }
}
