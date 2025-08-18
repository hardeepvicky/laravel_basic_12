<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\LaravelExtend;
use App\Models\Campaign;
use App\Models\Carrier;
use App\Models\City;
use App\Models\Country;
use App\Models\Dealer;
use App\Models\DispatchOrder;
use App\Models\DispatchOrderItem;
use App\Models\DispatchOrderProduct;
use App\Models\DocumentFile;
use App\Models\DropDownValue;
use App\Models\FieldSetting;
use App\Models\Item;
use App\Models\Lead;
use App\Models\LeadAddress;
use App\Models\LeadHistory;
use App\Models\LeadItem;
use App\Models\LeadProduct;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\Product;
use App\Models\State;
use App\Models\Type;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Rules\MobileNumber;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class DispatchOrderController extends BackendController
{
    public String $routePrefix = "dispatch-order";

    protected $modelClass = DispatchOrder::class;

    protected DispatchOrder | null $model = null;

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
            ["field" => "lead_id", "type" => "int", "cache" => false],
            ["field" => "type", "type" => "string"],
            ["field" => "status_type", "type" => "string"],
            ["field" => "carrier_id", "type" => "int"],
            ["field" => "tracking_no", "type" => "string"],
            ["field" => "builty_no", "type" => "string"],
            ["field" => "docket_no", "type" => "string"],
            ["field" => "pincode", "type" => "string"],
            ["field" => "country_id", "type" => "int"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "district_id", "type" => "int"],
            ["field" => "city_id", "type" => "int"],
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::where($conditions);

        $builder->with([
            "country", "state", "district", "city",
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

        if (isset($condition_list['country_id']))
        {
            $country_id = $condition_list['country_id'];

            $state_list = State::getList("id", "display_name", ["country_id" => $country_id]);

            $state_conditions = [
                ["country_id", $country_id],
                ["is_active", 1]
            ];

            $district_conditions = [
                ["is_active", 1]
            ];

            if (isset($condition_list['state_id']))
            {
                $state_conditions[] = ["id", $condition_list['state_id']];
                $district_conditions[] = ["state_id", $condition_list['state_id']];
            }
            
            $district_cities = City::getWithDistrict($state_conditions, $district_conditions, [["is_active", 1]]);
        
            $district_city_collection = collect($district_cities);

            $district_city_list = $district_city_collection->pluck('name', 'id')->toArray();
        }
        else
        {
            $state_list = $district_city_list = [];
        }

        $this->setForView(compact("state_list", "district_city_list"));

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $country_list = Country::getList("id", "name");

        $type_conditions = [
            'type' => Type::getType($this->model, "status_type")
        ];

        if ($this->model->status_type)
        {
            $type_conditions['or_id'] = Type::getIdFromCode("status_type", $this->model->status_type);
        }

        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $carrier_list = Carrier::getList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact(
            "country_list", "status_type_list", "carrier_list",
            "field_labels", 
        ));

        return parent::beforeViewRender();
    }

    private function _common_validation_rules()
    {
        return [
            "name" => ["required"],
            "mobile" => ["required", new MobileNumber],
            "mobile2" => ["nullable", new MobileNumber],
            "state_id" => ["required"],
            "city_id" => ["required"],
            "assign_to_user_id" => ["required"],
            "status_type" => ["required"],
            "company_name" => ["nullable"],
            "brand_name" => ["nullable"],
            "company_gst_no" => ["nullable"],
            "source_type" => ["nullable"],
            "source_id" => ["nullable"],
            "deal_type" => ["required"],
            "dealer_id" => [],
            "campaign_id" => ["nullable"],
            "purpose_price" => ["nullable"],
            "payment_method_type" => ["nullable"],
            "profile_img" => ["nullable"],
            "remarks" => ["nullable"],
            "leadFile" => [],
            "addresses" => [],
            "products" => [],
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    private function _beforeSave($validatedData)
    {
        if (isset($validatedData['city_id']) && $validatedData['city_id'])
        {
            $my_country = Country::getMyCountry();

            $district_cities = City::getWithDistrict([["country_id", $my_country->id]], [["is_active", 1]], [["is_active", 1]]);

            foreach($district_cities as $city)
            {
                if ($city['id'] == $validatedData['city_id'])
                {
                    $validatedData['district_id'] = $city['district_id'];
                    break;
                }
            }

            if (!isset($validatedData['district_id']))
            {
                LaravelExtend::addError("City", "District ID not found");
            }
        }

        return $validatedData;
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['leadFile'])) {

            $ids = explode(",", $validatedData['leadFile']);

            $document_files = DocumentFile::find($ids);

            $this->model->documentFile()->sync($document_files);
        }
    }

    public function edit($id)
    {
        $this->model = $model = $this->modelClass::with([])->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        $this->model = $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getRequiedIfRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        // dd($rules);

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $save_data = $this->_beforeSave($validatedData);
            unset($save_data['leadFile']);

            $this->model->fill($save_data);

            $this->model->save();

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new UserException("There are some errors", UserException::ERROR_USER_DATA_WRONG);
            }

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
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

        $type_list = Type::getListCache("code", "display_name");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $user_list = [];

            foreach($record['user'] as $user)
            {
                $user_list[] = $user['display_name'];
            }

            $tag_list = [];

            foreach($record['tags'] as $tag)
            {
                $tag_list[] = $tag['display_name'];
            }

            $csv_records[] = [
                'ID' => $record['id'],

                $field_labels['assign_to_user_id'] => $record['assign_to_user']['display_name'],
                $field_labels['dept_id'] => $record['department']['display_name'],
                $field_labels['task_type'] => get_value_from_list($type_list, $record['task_type']),
                $field_labels['name'] => $record['name'],
                $field_labels['description'] => $record['description'] ?? "",
                $field_labels['task_type'] => get_value_from_list($type_list, $record['task_type']),
                $field_labels['start_date_time'] => if_date_time($record['start_date_time']),
                $field_labels['duration'] => $record['duration'],
                $field_labels['duration_type'] => get_value_from_list($type_list, $record['duration_type']),
                $field_labels['end_date_time'] => if_date_time($record['end_date_time']),
                $field_labels['priority'] => $record['priority'],
                $field_labels['status'] => get_value_from_list($type_list, $record['status']),
                $field_labels['is_draft'] => get_value_from_list($yes_no_list, $record['is_draft']),
                $field_labels['is_completed'] => get_value_from_list($yes_no_list, $record['is_completed']),
                $field_labels['complete_date_time'] => if_date_time($record['complete_date_time']),
                $field_labels['complete_by'] => get_value_from_list($user_cache_list, $record['complete_by']),
                $field_labels['is_verified'] => get_value_from_list($yes_no_list, $record['is_verified']),
                $field_labels['verified_date_time'] => if_date_time($record['verified_date_time']),
                $field_labels['verified_by'] => get_value_from_list($user_cache_list, $record['verified_by']),
                $field_labels['rating'] => $record['rating'] ?? "",
                $field_labels['review_remarks'] => $record['review_remarks'] ?? "",
                $field_labels['taskUser'] => implode(", ", $user_list),
                $field_labels['tag_name'] => implode(", ", $tag_list),

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
        $file = $path . $this->tableName . "_$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }
}
