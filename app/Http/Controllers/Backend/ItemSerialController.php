<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\AutoSerialPattern;
use App\Models\Branch;
use App\Models\FieldSetting;
use App\Models\Item;
use App\Models\ItemBrand;
use App\Models\ItemSerial;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ItemSerialController extends BackendController
{
    public String $routePrefix = "item-serial";

    protected $modelClass = ItemSerial::class;
    protected ItemSerial | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "item",
            "make",
            "branch"
        ]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $item_list = Item::getList("id", "display_name", [], "list_display_order");

        $item_brand_list = ItemBrand::getList("id", "display_name", [], "list_display_order");

        $this->setForView(compact("records", "item_list", "item_brand_list"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "item_id", "type" => "int"],
            ["field" => "item_brand_id", "type" => "int"],
            ["field" => "branch_id", "type" => "int"],
            ["field" => "serial_no", "type" => "string"],
            ["field" => "is_in", "type" => "int"],
            ["field" => "is_damage", "type" => "int"],
        ]);

        $builder = $this->modelClass::query();

        $builder = $builder->where($conditions);

        if ($apply_sort) {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $this->model = new $this->modelClass;

            $sort = $this->getSortBy($this->model, $cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort['sort_by'], $sort['sort_dir']);
        }

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("branch_list", "field_labels"));

        return parent::beforeViewRender();
    }

    public function create()
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $model->no_of_serials = 1;

        $this->setForView(compact("model", 'form'));

        $this->set_data_for_add_edit($model);

        return $this->view(__FUNCTION__);
    }

    private function _common_validation_rules()
    {
        return [
            "item_id" => ["required"],
            "item_brand_id" => ["required"],
            "serial_no" => ["required"],
            "package_qty" => ["required", "numeric", "min:1", "max:10000"],
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    private function set_data_for_add_edit($model)
    {
        $auto_next_serial_no = ItemSerial::getAutoNextSerialNo();

        $manual_next_serial_no = ItemSerial::getManualNextSerialNo();

        $type_list = [
            "auto" => "Auto",
            "manual" => "Manual",
        ];

        $this->setForView(compact("auto_next_serial_no", "manual_next_serial_no", "type_list"));
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

        $rules['serial_type'] = ["required"];
        $rules['no_of_serials'] = ["nullable", "numeric", "min:1", "max:1000"];

        $messages = $this->_common_validation_messages();

        $this->model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData, true);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;

            $auth_user = Auth::user();

            if (!$auth_user->branch_id) {
                throw new UserException("No branch is assigned to you");
            }

            $save_data['branch_id'] = $auth_user->branch_id;
            $save_data['is_in'] = 1;

            $save_data['serial_no'] = str_replace("-", "", $save_data['serial_no']);

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);

            for ($i = 0; $i < $save_data['no_of_serials']; $i++) {

                $this->model = new $this->modelClass;
                $this->model->fill($save_data);

                if (!$this->model->save()) {
                    throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
                }

                $this->_afterSave();

                $save_data['serial_no'] = ItemSerial::getNextSerialNo($save_data['serial_no']);
                AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_PRODUCT_SERIAL);
            }

            StockItem::changeQty($this->model->branch_id, null, $this->model->item_id, null, $save_data['no_of_serials'] * $save_data['package_qty']);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            DB::commit();

            $save_and_close = (bool) request("save_and_close", false);

            if ($save_and_close)
            {
                return redirect()->to(get_current_path_url([
                    'last_insert_id' => $this->model->id,
                ]));
            }

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _afterSave()
    {
        
    }

    private function _saveOthers($validatedData)
    {
        
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

        $model->convertTimeZoneForDisplay();

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $this->set_data_for_add_edit($model);

        $this->setForView(compact("model", "form"));

        return $this->view(__FUNCTION__);
    }

    public function update($id, Request $request)
    {
        $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));
        // d($rules);

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData);

        DB::beginTransaction();

        try {

            $save_data = $validatedData;

            unset($save_data['children']);

            $save_data['serial_no'] = str_replace("-", "", $save_data['serial_no']);

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($save_data);
            // d($save_data);

            $this->model->fill($save_data);
            // d($this->model->manufacture_date_time, true);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_afterSave();

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            $this->updateUserInfoAfterSave($this->model);

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    public function export_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "item",
            "item.rootItemCategory",
            "make",
            "branch",
            "bin"
        ]);

        $records = $builder->get();

        foreach ($records as $k => $record) {
            $record->afterFindForCsv();
        }

        $records = $records->toArray();

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach ($records as $record) {

            $csv_records[] = [
                'ID' => $record['id'],
                'Category' => $record['item']['root_item_category']['display_name'] ?? "",
                $field_labels['item_id'] => $record['item']['display_name'] ?? "",
                $field_labels['item_brand_id'] => $record['make']['display_name'] ?? "",
                $field_labels['serial_type'] => $record['serial_type'],
                $field_labels['serial_no'] => "`" . $record['serial_no'],
                $field_labels['branch_id'] => $record['branch']['display_name'] ?? "",                
                $field_labels['bin_id'] => $record['bin']['display_name'] ?? "",                
                $field_labels['is_in'] => get_value_from_list($yes_no_list, $record['is_in']),
                $field_labels['is_damage'] => get_value_from_list($yes_no_list, $record['is_damage']),

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
        $file_prefix = str_class_name_to_human_text($this->modelClass);
        $file = $path . $file_prefix . "-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }

    public function ajax_get_list()
    {
        $response = ["status" => 1];

        $records = $this->modelClass::with([
            "item",
        ])->get();

        $response['data'] = [];

        foreach ($records as $record) {
            
            $display = $record->serial_no;

            $display .= " (" . ($record->item?->sku ?? "-") . ")";

            $row = [
                "id" => $record->id,
                "name" => $display,                    
            ];

            $response['data'][] = $row;
        }

        return $this->responseJson($response);
    }
}
