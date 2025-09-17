<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleOutput;
use App\Helpers\DateUtility;
use App\Helpers\MyCommand;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\RecurringTask;
use App\Models\Reminder;
use App\Models\SqlLog;
use App\Models\Task;
use App\Models\ViewContact;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReminderCommand extends MyCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remind';

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

            $today = date("Y-m-d");

            $today_lead_followups = Lead::whereDate("next_meeting_date_time", $today)->whereIn("status_type", [Lead::STATUS_HOT, Lead::STATUS_WARM, Lead::STATUS_COLD])->get();

            $today_lead_followup_count = [];
            foreach($today_lead_followups as $lead)
            {
                if ($lead->assign_to_user_id)
                {
                    if (!isset($today_lead_followup_count[$lead->assign_to_user_id]))
                    {
                        $today_lead_followup_count[$lead->assign_to_user_id] = 0;
                    }

                    $today_lead_followup_count[$lead->assign_to_user_id]++;
                }
            }

            foreach($today_lead_followup_count as $assign_to_user_id => $count)
            {
                $save = [
                    "from_date" => $today,
                    "to_date" => $today,
                    "msg" => "Today, You have $count sale lead followup",
                    "priority" => 1
                ];

                Reminder::insertOrUpdate($save, $is_insert, $is_update);
            }

            $today_customer_followups = Customer::whereDate("next_meeting_date_time", $today)->whereIn("status_type", [Lead::STATUS_HOT, Lead::STATUS_WARM, Lead::STATUS_COLD])->get();

            $today_customer_followup_count = [];
            foreach($today_customer_followups as $lead)
            {
                if ($lead->assign_to_user_id)
                {
                    if (!isset($today_customer_followup_count[$lead->assign_to_user_id]))
                    {
                        $today_customer_followup_count[$lead->assign_to_user_id] = 0;
                    }

                    $today_customer_followup_count[$lead->assign_to_user_id]++;
                }
            }

            foreach($today_customer_followup_count as $assign_to_user_id => $count)
            {
                $save = [
                    "from_date" => $today,
                    "to_date" => $today,
                    "msg" => "Today, You have $count sale customer followup",
                    "priority" => 1
                ];

                Reminder::insertOrUpdate($save);
            }


            $output = new ConsoleOutput();

            $counts = array_human_keys($counters);

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
