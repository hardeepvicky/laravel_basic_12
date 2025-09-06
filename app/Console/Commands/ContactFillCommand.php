<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleOutput;
use App\Helpers\MyCommand;
use App\Models\Contact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ContactFillCommand extends MyCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contact:fill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save data from Lead, Customer, Party, Dealer, etc.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->logCreate([
            "command" => $this->signature,
            "description" => $this->description
        ]);

        $output = new ConsoleOutput();

        $list = Contact::getRefListFromUnionOfTables();

        $counts = [
            "total" => count($list),
            "insert" => 0,
            "update" => 0,
            "delete" => 0,
        ];

        $records = Contact::get();

        $existing_list = [];

        foreach($records as $record)
        {
            $existing_list[$record->type][$record->ref_id] = $record;
        }

        DB::beginTransaction();

        foreach($list as $record)
        {
            unset($record['name']);

            $is_insert = $is_update = false;
            $contact = Contact::insertOrUpdate($record, $is_insert, $is_update, true);

            if($is_insert)
            {
                $counts['insert']++;
            }
            else if($is_update)
            {
                $counts['update']++;
            }

            unset($existing_list[$record['type']][$record['ref_id']]);
        }

        $id_list = [];
        foreach($existing_list as $type => $arr)
        {
            foreach($arr as $inner_obj)
            {
                $id_list[] = $inner_obj->id;
            }
        }

        Contact::destroy($id_list);

        DB::commit();

        $counts = array_human_keys($counts);

        $output->addLog($counts);

        $output->writeLogToConsole();

        $output->clearLogs();

        $this->output_lines = $output->output_lines;

        $this->logUpdate([
            "counts" => $counts
        ]);
    }
}
