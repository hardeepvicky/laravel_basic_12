<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\ImportUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\FieldSetting;
use App\Models\ImportLog;
use App\Models\Maker;
use App\Models\ManufactureOrder;
use App\Models\Product;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class ManufactureOrderController extends BackendController
{
    public String $routePrefix = "manufacture-order";

    protected $modelClass = ManufactureOrder::class;

    protected ManufactureOrder | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);
        // dd($records->toArray());

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $export_order_condition_list = $this->getConditions($cache_key . ".1", [
            ["field" => "export_order_no", "type" => "string"],
        ], true);

        $sale_order_condition_list = $this->getConditions($cache_key . ".2", [
            ["field" => "sale_order_no", "type" => "string"],
        ], true);

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "status", "type" => "string_equal"],
            ["field" => "product_id", "type" => "int"],
            ["field" => "make_id", "type" => "int"],
            ["field" => "assign_to_user_id", "type" => "int"],
            ["field" => "manufacture_complete_by", "type" => "int"],
            ["field" => "from_order_date_time", "type" => "date"],
            ["field" => "to_order_date_time", "type" => "date"],
            ["field" => "from_manufacture_complete_date_time", "type" => "date"],
            ["field" => "to_manufacture_complete_date_time", "type" => "date"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::query();

        if (isset($condition_list['from_order_date_time']))
        {
            $builder->whereDate('order_date_time', ">=", $condition_list['from_order_date_time']);
        }

        if (isset($condition_list['to_order_date_time']))
        {
            $builder->whereDate('order_date_time', "<=", $condition_list['to_order_date_time']);
        }

        if (isset($condition_list['from_manufacture_complete_date_time']))
        {
            $builder->whereDate('manufacture_complete_date_time', ">=", $condition_list['from_manufacture_complete_date_time']);
        }

        if (isset($condition_list['to_manufacture_complete_date_time']))
        {
            $builder->whereDate('manufacture_complete_date_time', "<=", $condition_list['to_manufacture_complete_date_time']);
        }

        $conditions = $array_helper->unsetValues($conditions, ["from_order_date_time", "to_order_date_time", "from_manufacture_complete_date_time", "to_manufacture_complete_date_time"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

        $builder->where($conditions);

        $builder->with([
            "product", 
            "make",
            "saleOrderProduct.saleOrder",
            "exportOrderProduct.exportOrder"
        ]);

        if (isset($sale_order_condition_list['sale_order_no']))
        {
            $builder->whereHas("saleOrderProduct", function($q) use($sale_order_condition_list) {
                $q->whereHas("saleOrder", function($q)  use($sale_order_condition_list) {
                    $q->where("order_no", "like", "%" . $sale_order_condition_list['sale_order_no'] . "%");
                });
            });
        }

        if (isset($export_order_condition_list['export_order_no']))
        {
            $builder->whereHas("exportOrderProduct", function($q) use($export_order_condition_list) {
                $q->whereHas("exportOrder", function($q)  use($export_order_condition_list) {
                    $q->where("order_no", $export_order_condition_list['export_order_no']['op'], "%" . $export_order_condition_list['export_order_no']['value'] . "%");
                });
            });
        }

        if ($apply_sort)
        {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $this->model = new $this->modelClass();

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
        if (!parent::beforeViewRender()) {
            return false;
        }

        $product_list = Product::getList("id", "display_name", [], "list_display_order");

        $make_list = Maker::getList("id", "display_name", [], "list_display_order");

        $user_list = User::getList("id", "display_name", ["type" => User::TYPE_INTERNAL]);

        $status_list = ManufactureOrder::getStatusList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);
        
        $this->setForView(compact("product_list", "make_list", "user_list", "status_list", "field_labels"));
    }


    private function _common_validation_rules()
    {
        return [
            'assign_to_user_id' => ["required"],
            'manufactured_qty' => ["required"],
            'manufacture_complete_date_time' => [],
            'manufacture_complete_by' => [],            
            'remarks' => ["nullable", "max:255"],
        ];
    }

    private function _common_validation_messages()
    {
        return [
        ];
    }

    public function edit($id)
    {
        $this->model = $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

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

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));
        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getRequiedIfRules($request));

        $rules["manufactured_qty"][] = "numeric";
        $rules["manufactured_qty"][] = "max:" . $this->model->order_qty;

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try
        {
            $validatedData = $this->model->convertTimeZoneBeforeQueryToDatabase($validatedData);

            $model->fill($validatedData);

            $model->save();

            DB::commit();

            return $this->afterSave($model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }

    public function export_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "product", 
            "make",
            "saleOrderProduct.saleOrder",
            "exportOrderProduct.exportOrder"
        ]);

        $records = $builder->get();

        foreach ($records as $k => $record) {
            $record->afterFindForCsv();
        }

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $status_list = ManufactureOrder::getStatusList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach ($records as $record) {

            $csv_records[] = [
                'ID' => $record['id'],
                "Sale Order No." => $record->saleOrderProduct?->saleOrder->getDisplayName() ?? "",
                "Sale Order No." => $record->exportOrderProduct?->exportOrder->getDisplayName() ?? "",
                $field_labels['product_id'] => $record->product->getDisplayName(),
                $field_labels['make_id'] => $record->make->getDisplayName(),
                $field_labels['assign_to_user_id'] => get_value_from_list($user_cache_list, $record->assign_to_user_id),
                $field_labels['order_qty'] => $record->order_qty ?? "",
                $field_labels['manufactured_qty'] => $record->manufactured_qty ?? "",
                $field_labels['status'] => get_value_from_list($status_list, $record->status),
                $field_labels['order_date_time'] => if_date_time($record->order_date_time),
                $field_labels['manufacture_complete_date_time'] => if_date_time($record->manufacture_complete_date_time),
                $field_labels['manufacture_complete_by'] => get_value_from_list($user_cache_list, $record->manufacture_complete_by),
                $field_labels['remarks'] => $record->remarks ?? "",

                'Created' => if_date_time($record->created_at),
                'Created By' => $user_cache_list[$record->created_by] ?? "",
                'Updated' => if_date_time($record->updated_at),
                'Updated By' => $user_cache_list[$record->updated_by] ?? "",
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
}
