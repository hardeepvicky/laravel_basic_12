<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Models\DropDownValue;
use App\Models\FieldSetting;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class CustomerController extends LeadController
{
    public String $routePrefix = "customer";

    protected $modelClass = Lead::class;

    protected Lead | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "status_type", "type" => "string"],
            ["field" => "name", "type" => "string"],
            ["field" => "mobile", "type" => "string"],
            ["field" => "mobile2", "type" => "string"],
            ["field" => "email", "type" => "string"],
            ["field" => "source_type", "type" => "string"],
            ["field" => "source_id", "type" => "int"],
            ["field" => "deal_type", "type" => "string"],
            ["field" => "dealer_id", "type" => "int"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "district_id", "type" => "int"],
            ["field" => "city_id", "type" => "int"],
            ["field" => "campaign_id", "type" => "int"],
        ]);

        $builder = $this->modelClass::where($conditions);
        $builder->where("is_customer", 1);

        $builder->with([
            "state", "district", "city",
            "address" => function($q){
                $q->orderBy("list_display_order");
            },
            "documentFile"
        ]);

        if ($apply_sort) {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $this->model = new $this->modelClass;

            $sort_params = $this->getSortBy($this->model, $cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);
        }

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (!parent::beforeViewRender())
        {
            return false;
        }

        $this->viewPrefix = "backend.Lead";
    }

    public function convert_to_lead($lead_id)
    {
        $lead = $this->modelClass::findOrFail($lead_id);

        $lead->is_customer = 0;
        $lead->status_type = Lead::STATUS_CONVERTED;

        $lead->save();

        return redirect()->route("lead.index")->with('success', "Convert to Lead Successfully");
    }

    
    public function export_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $records = $builder->get();

        foreach($records as $k => $record)
        {
            $record->afterFindForCsv();
        }

        $records = $records->toArray();

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $type_group_list = Type::getListOfModel(Lead::class);

        $drop_down_list = DropDownValue::getListCache("id", "display_name");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['name'] => $record['name'],
                $field_labels['mobile'] => $record['mobile'],
                $field_labels['mobile2'] => $record['mobile2'] ?? "",
                $field_labels['email'] => $record['email'] ?? "",
                $field_labels['status_type'] => get_value_from_list($type_group_list['status_type'], $record['status_type']),
                $field_labels['assign_to_user_id'] => $record['assign_to']['display_name'] ?? "",
                $field_labels['source_type'] => get_value_from_list($type_group_list['source_type'], $record['source_type']),
                $field_labels['source_id'] => get_value_from_list($drop_down_list, $record['source_id']),
                $field_labels['deal_type'] => get_value_from_list($type_group_list['deal_type'], $record['deal_type']),
                $field_labels['dealer_id'] => $record['dealer']['name'] ?? "",
                $field_labels['state_id'] => $record['state']['name'] ?? "",
                $field_labels['district_id'] => $record['district']['name'] ?? "",
                $field_labels['city_id'] => $record['city']['name'] ?? "",
                $field_labels['company_name'] => $record['company_name'] ?? "",
                $field_labels['brand_name'] => $record['brand_name'] ?? "",
                $field_labels['company_gst_no'] => $record['company_gst_no'] ?? "",
                $field_labels['remarks'] => $record['remarks'] ?? "",

                'Created' => if_date_time($record['created_at']),
                'Created By' => $user_cache_list[$record['created_by']] ?? "",
                'Updated' => if_date_time($record['updated_at']),
                'Updated By' => $user_cache_list[$record['updated_by']] ?? "",
            ];
        }

        // d($csv_records, true);

        $this->saveSqlLog();

        $path = laravel_constant('path.temp');
        FileUtility::createFolder($path);

        $current_dt = DateUtility::getDateObj(null);
        $current_dt->setTimezone(DateUtility::$login_user_time_zone);
        $datetime = DateUtility::getDate($current_dt, DateUtility::DATETIME_OUT_FORMAT_FILE);
        $file = $path . "Customer-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }

    public function export_csv_history()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with(["history"]);

        $this->beforeCsvExport($builder);

        $records = $builder->get();

        foreach($records as $k => $record)
        {
            $record->afterFindForCsv();
        }

        $records = $records->toArray();

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $type_group_list = Type::getListOfModel(Lead::class);

        $history_type_group_list = Type::getListOfModel(LeadHistory::class);

        $drop_down_list = DropDownValue::getListCache("id", "display_name");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $history_field_labels = FieldSetting::getDynamicFields(LeadHistory::class);

        foreach($records as $record)
        {
            $base_row = [
                'Lead ID' => $record['id'],
                $field_labels['name'] => $record['name'],
                $field_labels['mobile'] => $record['mobile'],
                $field_labels['mobile2'] => $record['mobile2'] ?? "",
                $field_labels['email'] => $record['email'] ?? "",
            ];

            foreach($record['history'] as $history)
            {
                $row = array_merge($base_row, [
                    $history_field_labels['entry_by'] => $history['entry_by'],
                    $history_field_labels['action_type'] => get_value_from_list($history_type_group_list['action_type'], $history['action_type']),
                    $history_field_labels['action'] => $history['action'] ?? "",
                    $history_field_labels['response_type_id'] => get_value_from_list($drop_down_list, $history['response_type_id']),
                    $history_field_labels['response'] => $history['response'] ?? "",
                    $history_field_labels['status_type'] => get_value_from_list($type_group_list['status_type'], $history['status_type']),
                    $history_field_labels['remarks'] => $history['remarks'] ?? "",
                    $history_field_labels['next_meeting_date_time'] => $history['next_meeting_date_time'] ? if_date_time($history['next_meeting_date_time']) : "",
                    $history_field_labels['is_lead_transfered'] => get_value_from_list($yes_no_list, $history['is_lead_transfered']),
                    $history_field_labels['assign_to_user_id'] => get_value_from_list($user_cache_list, $history['assign_to_user_id']),

                    'Created' => if_date_time($history['created_at']),
                    'Created By' => $user_cache_list[$history['created_by']] ?? "",
                    'Updated' => if_date_time($history['updated_at']),
                    'Updated By' => $user_cache_list[$history['updated_by']] ?? "",
                ]);

                $csv_records[] = $row;
            }
        }

        // d($csv_records, true);

        $this->saveSqlLog();

        $path = laravel_constant('path.temp');
        FileUtility::createFolder($path);

        $current_dt = DateUtility::getDateObj(null);
        $current_dt->setTimezone(DateUtility::$login_user_time_zone);
        $datetime = DateUtility::getDate($current_dt, DateUtility::DATETIME_OUT_FORMAT_FILE);
        $file = $path . "Customer History-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }
}
