<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleOutput;
use App\Helpers\DateUtility;
use App\Helpers\MyCommand;
use App\Models\Lead;
use App\Models\Notification;
use App\Models\RecurringTask;
use App\Models\SqlLog;
use App\Models\Task;
use App\Notifications\GeneralNotification;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LeadCommand extends MyCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lead:snooze';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for generating notification for leads which snoozed';

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

            $now = DateUtility::getDate(null, DateUtility::DATETIME_FORMAT);
            $last_one_minute = DateUtility::change($now, -2, DateUtility::MINTUES, DateUtility::DATETIME_FORMAT);
            
            $qb = Lead::where("is_snooze", 1)                
                ->where("snooze_datetime", ">=", $last_one_minute)
                ->where("snooze_datetime", "<=", $now)
                ->where("assign_to_user_id", ">", 0);
            
            SqlLog::addSelectQuery($qb);
            
            $records = $qb->get();

            $counters['found'] += $records->count();

            if ($records && $records->count() > 0)
            {
                foreach($records as $record)
                {
                    $msg = "Reminder : ";
                    $msg .= "Please attend " . $record->getDisplayName();

                    $link = "";
                    if ($record->is_customer)
                    {
                        $msg .= " (Customer)";
                        $link = route("customer.history", [$record]);
                    }
                    else
                    {
                        $msg .= " (Lead)";
                        $link = route("lead.history", [$record]);
                    }

                    $notification_data = [
                        "sub_type" => Notification::SUB_TYPE_INFO,
                        "msg" => $msg,
                        "link" => $link
                    ];

                    $record->assignTo?->notify(new GeneralNotification($notification_data));

                    $record->is_snooze = 0;
                    $record->save();

                    $counters['update']++;
                }
            }

            $output = new ConsoleOutput();

            $output->addLog($counters);

            $output->writeLogToConsole();

            $this->output_lines = $output->output_lines;

            $this->logUpdate([
                "counts" => $counters
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
