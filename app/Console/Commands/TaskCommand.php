<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleOutput;
use App\Helpers\DateUtility;
use App\Helpers\MyCommand;
use App\Models\AutomateTask;
use App\Models\CommandLog;
use App\Models\RecurringTask;
use App\Models\SqlLog;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TaskCommand extends MyCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for scheduling Tasks';

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
            $counters = [
                "found" => 0,
                "insert" => 0,
                "update" => 0,
                "delete" => 0,
            ];

            $main_counts = [
                "recurring_task" => $counters,
                "task" => $counters,
            ];

            $main_counts['task']['status_change'] = 0;

            $qb = RecurringTask::where("is_draft", 0);

            SqlLog::addSelectQuery($qb);
            
            $records = $qb->get();

            $main_counts['recurring_task']['found'] += $records->count();

            if ($records && $records->count() > 0)
            {
                foreach($records as $record)
                {
                    $main_counts['task']['insert'] += RecurringTask::createTask($record);
                }
            }

            $now = DateUtility::getDate(null, DateUtility::DATETIME_FORMAT);


            $qb = Task::where("status", Task::STATUS_CREATED)
                ->where("start_date_time", "<=", $now);

            SqlLog::addSelectQuery($qb);

            $records = $qb->get();

            $main_counts['task']['found'] += $records->count();

            if ($records && $records->count() > 0)
            {
                foreach($records as $record)
                {
                    if ($record->changeStatus(Task::STATUS_ASSIGNED))
                    {
                        $main_counts['task']['status_change']++;
                    }
                }
            }

            $output = new ConsoleOutput();

            $counts = array_human_keys($main_counts);

            $output->addLog($counts);

            $output->writeLogToConsole();

            $this->output_lines = $output->output_lines;

            $this->logUpdate([
                "counts" => $counts
            ]);

            $msg = $this->description . " : Completed successfully!";
            $this->info($msg);

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
