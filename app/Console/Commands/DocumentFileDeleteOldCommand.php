<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleOutput;
use App\Helpers\DateUtility;
use App\Helpers\MyCommand;
use App\Models\AutomateTask;
use App\Models\CommandLog;
use App\Models\DocumentFile;
use App\Models\RecurringTask;
use App\Models\SqlLog;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DocumentFileDeleteOldCommand extends MyCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document-file:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for delete old archive files';

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

            $_7_days_before = DateUtility::change($now, -7, DateUtility::DAYS);

            $qb = DocumentFile::where("is_archive", 1)->where("updated_at", "<", $_7_days_before);

            SqlLog::addSelectQuery($qb);
            
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
