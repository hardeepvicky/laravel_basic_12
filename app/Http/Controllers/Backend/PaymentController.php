<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\AutoSerialPattern;
use App\Models\DocumentFile;
use App\Models\FieldSetting;
use App\Models\Payment;
use App\Models\SaleOrder;
use App\Models\User;
use App\Models\ViewContact;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class PaymentController extends BackendController
{
    public String $routePrefix = "payment";

    protected $modelClass = Payment::class;

    protected Payment | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with(["saleOrder", "documentFile"]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);
        // dd($records->toArray());

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $so_condition_list = $this->getConditions($cache_key . ".1", [
            ["field" => "sale_order_no", "type" => "string"],            
        ]);

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "voucher_no", "type" => "string"],
            ["field" => "contact_id", "type" => "string"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::where($conditions);
        $builder->with(["contact"]);

        if (isset($so_condition_list['sale_order_no']))
        {
            $builder->whereHas("saleOrder", function($q) use ($so_condition_list){
                $q->where("order_no", $so_condition_list['sale_order_no']['op'],$so_condition_list['sale_order_no']['value']);
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

        $contact_list = ViewContact::getList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("contact_list", "field_labels"));
    }


    public function create(Request $request)
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        if ($request->get("sale_order_id"))
        {
            $so = SaleOrder::find($request->get("sale_order_id"));
            if ($so)
            {
                $model->contact_id = $so->contact_id;
                $model->sale_order_id = $so->id;
            }
        }

        $model->voucher_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_PAYMENT_VOUCHER);

        $this->setForView(compact("model", 'form'));

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            'contact_id' => ["required"],
            'amount' => ["required"],
            'sale_order_id' => ["nullable"],
            'remarks' => ["nullable"],
            "files" => [],
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $this->model = new $this->modelClass();

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try
        {
            list($order_from_type, $contact_id) = explode(".", $validatedData['contact_id']);

            $validatedData['order_from_type'] = $order_from_type;
            $validatedData['contact_id'] = $contact_id;
            $validatedData['voucher_no'] = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_PAYMENT_VOUCHER);

            $this->model = $this->modelClass::create($validatedData);

            AutoSerialPattern::increaseCount(AutoSerialPattern::TYPE_PAYMENT_VOUCHER);

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            DB::commit();

            return $this->afterSave($this->model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);;
        }
    }


    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['files'])) {

            $ids = explode(",", $validatedData['files']);

            $document_files = DocumentFile::find($ids);

            $this->model->documentFile()->sync($document_files);
        }
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

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try
        {
            list($order_from_type, $contact_id) = explode(".", $validatedData['contact_id']);

            $validatedData['order_from_type'] = $order_from_type;
            $validatedData['contact_id'] = $contact_id;

            $model->fill($validatedData);

            $model->save();

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            DB::commit();

            return $this->afterSave($model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);;
        }
    }

    public function copy($id)
    {
        $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", "form"));

        return $this->view("form");
    }

    public function ajax_get_list($sale_order_id = 0, $contact_id = 0)
    {
        $response = ["status" => 1, "data" => []];

        $conditions = [];

        if ($sale_order_id)
        {
            $conditions["sale_order_id"] = $sale_order_id;
        }

        if ($contact_id)
        {
            $conditions["contact_id"] = $contact_id;
        }

        $list = $this->modelClass::getList("id", "display_name", $conditions, "list_display_order");

        $response['data'] = [];

        foreach($list as $k => $v)
        {
            $response['data'][] = [
                'id' => $k,
                'name' => $v,
            ];
        }

        return $this->responseJson($response);
    }



    public function export_csv()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->beforeCsvExport($builder);

        $builder->with(["saleOrder"]);

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

            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['voucher_no'] => $record['voucher_no'] ?? "",
                $field_labels['sale_order_id'] => $record['sale_order']['order_no'] ?? "",
                $field_labels['contact_id'] => $record['contact']['name'] ?? "",                
                $field_labels['amount'] => $record['amount'] ?? "",                
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
        $file_prefix = str_class_name_to_human_text($this->modelClass);
        $file = $path . $file_prefix . "-$datetime.csv";

        $csvUtility = new CsvUtility($file);
        $csvUtility->write($csv_records);

        download_start($file, "application/octet-stream");
    }
}
