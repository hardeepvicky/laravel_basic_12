<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\Bin;
use App\Models\Branch;
use App\Models\FieldSetting;
use App\Models\Maker;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSerial;
use App\Models\StockProduct;
use App\Models\StockProductTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class StockProductController extends BackendController
{
    public String $routePrefix = "stock-product";

    protected $modelClass = StockProduct::class;

    protected StockProduct | null $model = null;

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
            ["field" => "branch_id", "type" => "int"],
            ["field" => "bin_id", "type" => "int"],
            ["field" => "product_id", "type" => "int"],
            ["field" => "make_id", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::where($conditions);
        $builder->with([
            "branch", "bin", "product", "make"
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

        $product_list = Product::getList("id", "display_name", [], "list_display_order");

        $this->setForView(compact("product_list"));

        $bin_list = [];

        if (isset($condition_list['branch_id'])) {
            $bin_list = Bin::getList("id", "display_name", ["branch_id" => $condition_list['branch_id']], "list_display_order");
        }

        $this->setForView(compact("bin_list"));

        return $builder;
    }

    protected function beforeViewRender()
    {
        if (is_null($this->model)) {
            $this->model = new $this->modelClass();
        }

        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");

        $make_list = Maker::getList("id", "display_name", [], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("branch_list", "make_list", "field_labels"));

        return parent::beforeViewRender();
    }

    public function transactions()
    {
        $cache_key_prefix = Route::currentRouteName();

        $cache_key = $cache_key_prefix . "-index";

        $product_conditions = $this->getConditions($cache_key . ".2", [
            ["field" => "product_category_id", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($product_conditions);
        $category_condition_list = $array_helper->convertModelSearchConditionToList();

        $stock_product_conditions = $this->getConditions($cache_key, [
            ["field" => "branch_id", "type" => "int"],
            ["field" => "bin_id", "type" => "int"],
            ["field" => "product_id", "type" => "int"],
            ["field" => "make_id", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($stock_product_conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = StockProductTransaction::whereHas("stockProduct", function ($q) use($stock_product_conditions, $product_conditions){
            $q->where($stock_product_conditions)->whereHas("product", function($q) use($product_conditions) {
                $q->where($product_conditions);
            });
        });

        $builder->with([
            "stockProduct",
            "stockProduct.branch", 
            "stockProduct.bin", 
            "stockProduct.product",
            "stockProduct.make",
        ]);

        $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

        $clear_cache = request('is_sort_clear', false);

        $this->model = new $this->modelClass;

        $sort_params = $this->getSortBy(new StockProductTransaction(), $cache_key_for_sort, [
            ["key" => "sort_by", "default" => "id"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        $product_list = Product::getList("id", "display_name", [], "list_display_order");

        if (isset($category_condition_list['product_category_id'])) {
            $product_list = Product::getList("id", "display_name", ["product_category_id" => $category_condition_list['product_category_id']], "list_display_order");
        }

        $this->setForView(compact("product_list"));

        $bin_list = [];

        if (isset($condition_list['branch_id'])) {
            $bin_list = Bin::getList("id", "display_name", ["branch_id" => $condition_list['branch_id']], "list_display_order");
        }

        $this->setForView(compact("bin_list"));

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    public function create()
    {
        $this->beforeCreate();

        $model = new $this->modelClass();

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $bin_list = Bin::getList("id", "display_name", [], "list_display_order");

        $product_list = Product::getList("id", "display_name", [], "list_display_order");
        
        $make_list = Maker::getList("id", "display_name", [], "list_display_order");

        $this->setForView(compact("model", 'form', 'bin_list', 'product_list', 'make_list'));


        return $this->view("form");
    }
    

    private function _common_validation_rules()
    {
        return [
            "bin_id" => ["required"],
            "product_id" => ["required"],
            "make_id" => ["required"],
            "qty" => ["required", "numeric", "min:1", "max:10000"],
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

        $model = new $this->modelClass();

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {

            $bin = Bin::findOrFail($validatedData['bin_id']);
            
            StockProduct::changeQty($bin->branch_id, $validatedData['bin_id'], $validatedData['product_id'], $validatedData['make_id'], $validatedData['qty']);

            DB::commit();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    public function destroy($stock_product_transaction_id)
    {
        $request = request();

        try {
            $model = StockProductTransaction::findOrFail($stock_product_transaction_id);

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

    public function in(Request $request)
    {
        if ($request->isMethod("post"))
        {
            $response = ["status" => 0];
            $data = $request->all();

            try
            {
                $required_fields = ["bin_code", "product_serial_no"];

                foreach($required_fields as $field)
                {
                    if (!isset($data[$field]) || empty($data[$field]))
                    {
                        throw new UserException("Missing or empty $field", UserException::ERROR_USER_DATA_WRONG);
                    }
                }

                $bin = Bin::where("code", $data['bin_code'])->first();

                if (!$bin)
                {
                    throw new UserException("Wrong Bin Code", UserException::ERROR_USER_DATA_WRONG);
                }

                $auth_user = Auth::user();

                if ($bin->branch_id != $auth_user->branch_id)
                {
                    $bin->load("branch");
                    $auth_user->load("branch");

                    $msg = "Bin's branch is " . $bin->branch->getDisplayName();
                    $msg = ", wheres you have branch " . $auth_user->branch->getDisplayName();
                    throw new UserException($msg, UserException::ERROR_USER_DATA_WRONG);
                }

                $product_serial_no = ProductSerial::where("serial_no", $data['product_serial_no'])->first();

                if (!$product_serial_no)
                {
                    throw new UserException("Wrong Serial No.", UserException::ERROR_USER_DATA_WRONG);
                }

                if (!$product_serial_no->make_id)
                {
                    throw new UserException("Serial No. have no make", UserException::ERROR_USER_DATA_WRONG);
                }

                DB::beginTransaction();

                if ($product_serial_no->is_in)
                {
                    if ($product_serial_no->bin_id == $bin->id)
                    {
                        throw new UserException("Serial No. is already In", UserException::ERROR_USER_DATA_WRONG);
                    }
                    else
                    {
                        StockProduct::changeQty($product_serial_no->branch_id, $product_serial_no->bin_id, $product_serial_no->product_id, $product_serial_no->make_id, -1);
                    }
                }

                StockProduct::changeQty($bin->branch_id, $bin->id, $product_serial_no->product_id, $product_serial_no->make_id, 1);

                $product_serial_no->markIn($bin);

                DB::commit();

                $response['status'] = 1;

            }
            catch(Exception $ex)
            {
                DB::rollBack();

                $response['status'] = 0;
                $response['msg'] = $ex->getMessage();
            }

            return $this->responseJson($response);
        }

        return $this->view(__FUNCTION__);
    }


    public function out(Request $request)
    {
        if ($request->isMethod("post"))
        {
            $response = ["status" => 0];
            $data = $request->all();

            try
            {
                $required_fields = ["product_serial_no"];

                foreach($required_fields as $field)
                {
                    if (!isset($data[$field]) || empty($data[$field]))
                    {
                        throw new UserException("Missing or empty $field", UserException::ERROR_USER_DATA_WRONG);
                    }
                }

                $product_serial_no = ProductSerial::where("serial_no", $data['product_serial_no'])->first();

                if (!$product_serial_no)
                {
                    throw new UserException("Wrong Serial No.", UserException::ERROR_USER_DATA_WRONG);
                }

                if (!$product_serial_no->make_id)
                {
                    throw new UserException("Serial No. have no make", UserException::ERROR_USER_DATA_WRONG);
                }

                DB::beginTransaction();

                if (!$product_serial_no->is_in)
                {
                    throw new UserException("Serial No. is already Out", UserException::ERROR_USER_DATA_WRONG);
                }

                StockProduct::changeQty($product_serial_no->branch_id, $product_serial_no->bin_id, $product_serial_no->product_id, $product_serial_no->make_id, -1);

                $product_serial_no->markOut();

                DB::commit();

                $response['status'] = 1;

            }
            catch(Exception $ex)
            {
                DB::rollBack();

                $response['status'] = 0;
                $response['msg'] = $ex->getMessage();
            }

            return $this->responseJson($response);
        }

        return $this->view(__FUNCTION__);
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

        $product_category_records = ProductCategory::getTreeRecords(ProductCategory::query());

        $product_category_list = Util::getTreeListArray($product_category_records, "id", "name", false, false);

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $csv_records[] = [
                'ID' => $record['id'],
                'Category' => get_value_from_list($product_category_list, $record['product']['product_category_id']),
                $field_labels['product_id'] => $record['product']['model_name'] ?? "",
                $field_labels['make_id'] => $record['make']['name'] ?? "",
                $field_labels['branch_id'] => $record['branch']['name'] ?? "",
                $field_labels['bin_id'] => $record['bin']['name'] ?? "",
                $field_labels['qty'] => $record['qty'],

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
