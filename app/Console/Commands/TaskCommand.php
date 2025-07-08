<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleOutput;
use App\Helpers\DateUtility;
use App\Helpers\MyCommand;
use App\Models\AutomateTask;
use App\Models\CommandLog;
use App\Models\RecurringTask;
use App\Models\Task;
use Carbon\Carbon;
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
            $counts = [
                'task_created' => 0,
                "task_status_change_to_assigned" => 0,
            ];
            
            $records = RecurringTask::where("is_draft", 0)->get();

            if ($records && $records->count() > 0)
            {
                foreach($records as $record)
                {
                    $counts['task_created'] += RecurringTask::createTask($record);
                }
            }

            $now = DateUtility::getDate(null, DateUtility::DATETIME_FORMAT);

            $records = Task::where("status", Task::STATUS_CREATED)
                ->where("start_date_time", "<=", $now)
                ->get();

            if ($records && $records->count() > 0)
            {
                foreach($records as $record)
                {
                    $record->changeStatus(Task::STATUS_ASSIGNED);
                    $counts['task_status_change_to_assigned']++;
                }
            }

            $this->logUpdate([
                "counts" => $counts
            ]);

            $output = new ConsoleOutput();

            $counts = array_human_keys($counts);

            $output->addLog($counts);

            $output->writeLogToConsole();

            $msg = $this->description . " : Completed successfully!";
            $this->info($msg); // Output to console
            Log::info($msg);

        } catch (\Exception $e) {
            $msg = "Command Error : " . $this->description . " : " . $e->getMessage();
            $this->error($msg); // Output to console
            Log::error($msg, ['exception' => $e]);
        }

        return Command::SUCCESS; // Indicate success
    }
}
