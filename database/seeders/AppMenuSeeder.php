<?php

namespace Database\Seeders;

use App\Console\Commands\ModelToJsonCommand;
use App\Exceptions\UserException;
use App\Helpers\ConsoleOutput;
use App\Helpers\Util;
use App\Models\AppMenu;
use App\Models\AppMenuRelatedLink;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AppMenuSeeder extends Seeder
{
    private ConsoleOutput $output;
    private array $counters = [
        "total_count" => 0,
        "insert_count" => 0,
        "update_count" => 0,
        "delete_count" => 0,
    ];

    private array $multi_counters = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->output = new ConsoleOutput();

        $model_class = AppMenu::class;

        $model_name = str_class_name_without_namespace($model_class);

        $filename = $model_name . ".json";

        $path = ModelToJsonCommand::$path . "/";

        $disk = ModelToJsonCommand::getStorageDisk();

        $content = $disk->get($path . $filename);

        $records = json_decode($content);

        if ($records === null && json_last_error() !== JSON_ERROR_NONE) {
            $this->output->addLogMessage("cound not decode json from " . $path . $filename, "error");
        } else {
            $tree_records = Util::getTreeArray(Util::objToArray($records), "parent_id");

            $this->save($tree_records);
        }

        foreach ($this->multi_counters as $label => $counts) {
            $this->output->addLogMessage(str_class_name_to_human_text($label));

            $counts = array_human_keys($counts);

            $this->output->addLog($counts);
        }

        $this->output->writeLogToConsole();
    }

    public function save($records, $parent_id = 0)
    {
        if (!isset($this->multi_counters["AppMenu"]))
        {
            $this->multi_counters["AppMenu"] = $this->counters;
        }

        $this->output->addLogMessage("Writing AppMenu With Parent : $parent_id");

        $model = new AppMenu();

        $is_insert = false;
        $is_update = false;

        $table_info = $model->getTableInfo($model->getTable());

        foreach ($records as $record) {
            $save = [];

            foreach ($table_info['columns'] as $field => $field_attr) {
                if (in_array($field, ["id"])) {
                    continue;
                }

                if (isset($record[$field])) {
                    $save[$field] = $record[$field];
                }
            }

            $save['parent_id'] = $parent_id;

            try {
                $model = AppMenu::insertOrUpdate($save, $is_insert, $is_update);
                // d($save, true);
                
                if ($is_insert) {
                    $this->multi_counters["AppMenu"]['insert_count']++;
                }

                if ($is_update) {
                    $this->multi_counters["AppMenu"]['update_count']++;
                }

                if (isset($record['children']) && is_array($record['children']) && count($record['children']) > 0)
                {
                    $this->save($record['children'], $model->id);
                }

                if (isset($record['child_records'])) {
                    $child_records = $record['child_records'];

                    if (isset($child_records['AppMenuRelatedLink']) && is_array($child_records['AppMenuRelatedLink'])) {
                        $app_menu_related_links = $child_records['AppMenuRelatedLink'];

                        $child_model = new AppMenuRelatedLink();

                        if (!isset($this->multi_counters['AppMenuRelatedLink']['total_count'])) {
                            $this->multi_counters['AppMenuRelatedLink'] = $this->counters;
                        }

                        foreach ($app_menu_related_links as $child_record) {
                            unset($child_record['id']);
                            unset($child_record['app_menu_id']);

                            $child_record['app_menu_id'] = $model->id;

                            $child_model = AppMenuRelatedLink::insertOrUpdate($child_record, $is_insert, $is_update);

                            if ($is_insert) {
                                $this->multi_counters['AppMenuRelatedLink']['insert_count']++;
                            }

                            if ($is_update) {
                                $this->multi_counters['AppMenuRelatedLink']['update_count']++;
                            }
                        }
                    }
                }
            } catch (UserException $ex) {
                if (isset($ex->data['custom_errors']) && is_array($ex->data['custom_errors'])) {
                    foreach ($ex->data['custom_errors'] as $group => $errors) {
                        foreach ($errors as $error) {
                            $this->output->addLogMessage($group . " : " . $error['label'] . " : " . $error['msg'], "error");
                        }
                    }
                } else {
                    throw $ex;
                }
            }
        }
    }

    public function getParentId($name)
    {
        // AppMenu::where("name", $name)->get();
    }
}
