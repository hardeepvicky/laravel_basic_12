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
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class PurchaseOrderPaymentController extends BackendController
{
    public String $routePrefix = "purchase-order-payment";

    protected $modelClass = PurchaseOrderPayment::class;

    protected PurchaseOrderPayment | null $model = null;

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

        $so_condition_list = $this->getConditions($cache_key . ".1", [
            ["field" => "purchase_order_no", "type" => "string"],
            ["field" => "party_id", "type" => "int"],
        ]);

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "voucher_no", "type" => "string"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::where($conditions);
        $builder->with(["purchaseOrder.party"]);

        if (isset($so_condition_list))
        {
            $builder->whereHas("purchaseOrder", function($q) use ($so_condition_list){
                if (isset($so_condition_list['purchase_order_no']['op']))
                {
                    $q->where("order_no", $so_condition_list['purchase_order_no']['op'],$so_condition_list['purchase_order_no']['value']);
                }

                if (isset($so_condition_list['party_id']['op']))
                {
                    $q->where("party_id", $so_condition_list['party_id']['op'],$so_condition_list['party_id']['value']);
                }
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

        $purchase_order_list = PurchaseOrder::getList("id", "display_name", ["status" => [PurchaseOrder::STATUS_APPROVE, PurchaseOrder::STATUS_RECEIVED]], "id", "DESC");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("purchase_order_list", "field_labels"));
    }


    public function create(Request $request)
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        if ($request->get("purchase_order_id"))
        {
            $po = PurchaseOrder::find($request->get("purchase_order_id"));

            if ($po)
            {
                $model->purchase_order_id = $po->id;
            }
        }

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $model->voucher_no = AutoSerialPattern::getNextCounter(AutoSerialPattern::TYPE_PAYMENT_VOUCHER);

        $this->setForView(compact("model", 'form'));

        return $this->view("form");
    }

    private function _common_validation_rules()
    {
        return [
            'purchase_order_id' => ["required"],
            'amount' => ["required"],
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

    public function ajax_get_list($purchase_order_id = 0)
    {
        $response = ["status" => 1, "data" => []];

        $conditions = [];

        if ($purchase_order_id)
        {
            $conditions["purchase_order_id"] = $purchase_order_id;
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
                $field_labels['purchase_order_id'] => $record['purchase_order']['order_no'] ?? "",
                'Party' => $record['purchase_order']['party']['display_name'] ?? "",
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
