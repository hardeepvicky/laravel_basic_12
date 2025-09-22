<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\FieldSetting;
use App\Models\Product;
use App\Models\ProductionTarget;
use App\Models\ProductionTargetComplete;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ProductionTargetController extends BackendController
{
    public String $routePrefix = "production-target";

    protected $modelClass = ProductionTarget::class;

    protected ProductionTarget | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "branch", 
            "product",
            "user", 
            "productionTarget",
            "productionTarget.employee",
        ]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

        $user_list = User::getList("id", "display_name");

        $this->setForView(compact("records", "user_list"));

        return $this->viewIndex(__FUNCTION__);
    }


    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix;

        $conditions = $this->getConditions($cache_key_prefix, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "branch_id", "type" => "int"],
            ["field" => "responsible_user_id", "type" => "int"],
            ["field" => "product_id", "type" => "int"],
            ["field" => "target_date", "type" => "date"],
        ]);

        $builder = $this->modelClass::where($conditions);

        $builder->with(["branch", "product", "user"]);

        if ($apply_sort) {
            $cache_key_for_sort = $cache_key_prefix . "-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $sort_params = $this->getRequestData($cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);
        }

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $product_list = Product::getList("id", "display_name", [], "list_display_order");

        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $production_target_complete_fields = FieldSetting::getDynamicFields(ProductionTarget::class);

        $this->setForView(compact(
            "branch_list", "product_list",
            "field_labels", "production_target_complete_fields"
        ));

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

        $this->setForView(compact("model", 'form'));
        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            'branch_id' => ['required'],
            'responsible_user_id' => ['required'],
            'product_id' => ['required'],
            'target_date' => ['required'],
            'qty' => ['required'],
            "remarks" => "",
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    public function store(Request $request)
    {
        $rules = $this->_common_validation_rules();
        $messages = $this->_common_validation_messages();

        $this->model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            
            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($validatedData);

            $this->model->fill($save_data);

            if (!$this->model->save()) {
                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("Fail to Save");
            }

            DB::commit();
            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();
            $this->saveSqlLog();
            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    public function edit($id)
    {
        $model = $this->modelClass::with([])->findOrFail($id);
        $model->afterFind();

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
        $this->model = $this->modelClass::with([])->findOrFail($id);

        $this->beforeEdit($this->model);

        $rules = $this->_common_validation_rules();
        $messages = $this->_common_validation_messages();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {

            $save_data = $this->model->convertTimeZoneBeforeQueryToDatabase($validatedData);

            $this->model->fill($save_data);
            if (!$this->model->save()) {
                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            if ($this->custom_errors) {
                throw new Exception("Fail to Save");
            }

            DB::commit();

            return $this->afterSave($this->model);
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->saveSqlLog();

            return back()->with('fail', get_exception_display_message($ex))->withInput()->with("custom_errors", $this->custom_errors);
        }
    }

    private function _saveOthers($validatedData)
    {
        
    }

    public function completion($id, Request $request)
    {
        $model = $this->model = $this->modelClass::with([
            "branch",
            "product",            
        ])->findOrFail($id);

        if ($request->isMethod("post"))
        {
            // dd($request->all());
            
            $rules = [
                "complete_by_employee_id" => ["required"],
                "qty" => ["required"],
                "remarks" => ["nullable"],
            ];
            
            $validatedData = $request->validate($rules);

            DB::beginTransaction();

            $validatedData['production_target_id'] = $id;
            $child_model = ProductionTargetComplete::create($validatedData);

            DB::commit();

            return redirect()->route($this->routePrefix . ".index")->with("success", "Record save successfully");
        }

        $form = [
            'url' => route(Route::currentRouteName(), [$id]),
            'method' => 'POST',
        ];

        $employee_list = Employee::getList("id", "display_name");

        $this->setForView(compact("model", "form", "employee_list"));

        return $this->view(__FUNCTION__);
    }


    public function completion_destroy($id, Request $request)
    {
        $model = ProductionTargetComplete::findOrFail($id);

        try {
            $this->beforeDelete($model);

            if (!$this->delete($model)) {
                throw new Exception("Fail to Delete");
            }

            return $this->afterDestroy($model);
        } catch (\Exception $ex) {
            if ($request->ajax()) {
                return $this->responseJson(['status' => 0, 'msg' => $ex->getMessage()]);
            }

            $this->saveSqlLog();

            return back()->with('fail', $ex->getMessage());
        }
    }


    public function export_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with([
            "branch", 
            "product",
            "user", 
            "productionTarget",
            "productionTarget.employee",
        ]);

        $records = $builder->get();

        foreach ($records as $k => $record) {
            $record->afterFindForCsv();
        }

        $records = $records->toArray();

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach ($records as $record) {

            foreach($record['production_target'] as $productionTarget)
            {
                $csv_records[] = [
                    'ID' => $record['id'],
                    $field_labels['branch_id'] => $record['branch']['display_name'] ?? "",
                    $field_labels['product_id'] => $record['product']['display_name'] ?? "",
                    $field_labels['responsible_user_id'] => $record['user']['display_name'] ?? "",
                    $field_labels['target_date'] => $record['target_date'] ?? "",
                    $field_labels['qty'] => $record['qty'] ?? "",
                    $field_labels['complete_qty'] => $record['complete_qty'] ?? "",
                    $field_labels['remarks'] => $record['remarks'] ?? "",
                    
                    "Employee" => $productionTarget['employee']['display_name'] ?? "",
                    "Work Qty" => $productionTarget['qty'] ?? "",
                    "Work Remarks" => $productionTarget['remarks'] ?? "",

                    'Created' => if_date_time($record['created_at']),
                    'Created By' => $user_cache_list[$record['created_by']] ?? "",
                    'Updated' => if_date_time($record['updated_at']),
                    'Updated By' => $user_cache_list[$record['updated_by']] ?? "",
                ];
            }
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
