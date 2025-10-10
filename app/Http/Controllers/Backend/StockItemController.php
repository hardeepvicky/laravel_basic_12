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
use App\Models\Item;
use App\Models\ItemBrand;
use App\Models\ItemSerial;
use App\Models\StockItem;
use App\Models\StockItemTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class StockItemController extends BackendController
{
    public String $routePrefix = "stock-item";

    protected $modelClass = StockItem::class;

    protected StockItem | null $model = null;

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
            ["field" => "item_id", "type" => "int"],
            ["field" => "brand_id", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::where($conditions);
        $builder->with([
            "branch", "bin", "item", "make"
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

        $item_list = Item::getList("id", "display_name", [], "list_display_order");

        $item_brand_list = ItemBrand::getList("id", "display_name", [], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("item_list", "branch_list", "item_brand_list", "field_labels"));

        return parent::beforeViewRender();
    }

    public function transactions()
    {
        $cache_key_prefix = Route::currentRouteName();

        $cache_key = $cache_key_prefix . "-index";

        $stock_product_conditions = $this->getConditions($cache_key, [
            ["field" => "branch_id", "type" => "int"],
            ["field" => "bin_id", "type" => "int"],
            ["field" => "item_id", "type" => "int"],
            ["field" => "brand_id", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($stock_product_conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = StockItemTransaction::whereHas("stockItem", function ($q) use($stock_product_conditions){
            $q->where($stock_product_conditions);
        });

        $builder->with([
            "stockItem",
            "stockItem.branch", 
            "stockItem.bin", 
            "stockItem.item",
            "stockItem.make",
        ]);

        $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

        $clear_cache = request('is_sort_clear', false);

        $this->model = new $this->modelClass;

        $sort_params = $this->getSortBy(new StockItemTransaction(), $cache_key_for_sort, [
            ["key" => "sort_by", "default" => "id"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

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

        $this->setForView(compact("model", 'form', 'bin_list'));

        return $this->view("form");
    }
    

    private function _common_validation_rules()
    {
        return [
            "bin_id" => ["required"],
            "item_id" => ["required"],
            "brand_id" => ["required"],
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
            
            StockItem::changeQty($bin->branch_id, $validatedData['bin_id'], $validatedData['item_id'], null, $validatedData['qty']);

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
            $model = StockItemTransaction::findOrFail($stock_product_transaction_id);

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
                $required_fields = ["branch_id", "item_serial_no"];

                foreach($required_fields as $field)
                {
                    if (!isset($data[$field]) || empty($data[$field]))
                    {
                        throw new UserException("Missing or empty $field", UserException::ERROR_USER_DATA_WRONG);
                    }
                }

                $branch = Branch::find($data['branch_id']);

                if (!$branch)
                {
                    throw new UserException("Wrong Branch", UserException::ERROR_USER_DATA_WRONG);
                }

                if (!$branch->is_active)
                {
                    throw new UserException("Branch is de-active", UserException::ERROR_USER_DATA_WRONG);
                }

                if (isset($data['bin_code']) && !empty($data['bin_code']))
                {
                    $bin = Bin::where("code", $data['bin_code'])->first();

                    if (!$bin)
                    {
                        throw new UserException("Wrong Bin Code", UserException::ERROR_USER_DATA_WRONG);
                    }

                    if ($bin->branch_id != $branch->id)
                    {
                        $bin->load("branch");

                        $msg = "Location's branch is " . $bin->branch->getDisplayName();
                        $msg = ", wheres selected branch is " . $branch->getDisplayName();
                        throw new UserException($msg, UserException::ERROR_USER_DATA_WRONG);
                    }
                }

                $auth_user = Auth::user();

                if ($branch->id != $auth_user->branch_id)
                {
                    $msg = $branch->getDisplayName() ." is not assign to you";
                    throw new UserException($msg, UserException::ERROR_USER_DATA_WRONG);
                }

                $item_serial_no = ItemSerial::where("serial_no", $data['item_serial_no'])->first();

                if (!$item_serial_no)
                {
                    throw new UserException("Wrong Serial No.", UserException::ERROR_USER_DATA_WRONG);
                }

                if (!$item_serial_no->item_brand_id)
                {
                    throw new UserException("Serial No. have no make", UserException::ERROR_USER_DATA_WRONG);
                }

                DB::beginTransaction();

                if ($item_serial_no->is_in)
                {
                    if (isset($bin))
                    {
                        if ($item_serial_no->bin_id == $bin->id)
                        {
                            throw new UserException("Serial No. is already In", UserException::ERROR_USER_DATA_WRONG);
                        }
                        else
                        {
                            StockItem::changeQty($item_serial_no->branch_id, $item_serial_no->bin_id, $item_serial_no->item_id, $item_serial_no->item_brand_id, -1 * $item_serial_no->package_qty);
                        }
                    }
                    else
                    {
                        if ($item_serial_no->branch_id == $branch->id)
                        {
                            throw new UserException("Serial No. is already In", UserException::ERROR_USER_DATA_WRONG);
                        }
                        else
                        {
                            StockItem::changeQty($item_serial_no->branch_id, null, $item_serial_no->item_id, $item_serial_no->item_brand_id, -1 * $item_serial_no->package_qty);
                        }
                    }
                }

                if (isset($bin))
                {
                    StockItem::changeQty($bin->branch_id, $bin->id, $item_serial_no->item_id, $item_serial_no->item_brand_id, 1 * $item_serial_no->package_qty);
                }
                else
                {
                    StockItem::changeQty($branch->id, null, $item_serial_no->item_id, $item_serial_no->item_brand_id, 1 * $item_serial_no->package_qty);
                }

                if (isset($bin))
                {
                    $item_serial_no->markIn($bin);
                }
                else
                {
                    $item_serial_no->markInBranch($branch);
                }

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
                $required_fields = ["item_serial_no"];

                foreach($required_fields as $field)
                {
                    if (!isset($data[$field]) || empty($data[$field]))
                    {
                        throw new UserException("Missing or empty $field", UserException::ERROR_USER_DATA_WRONG);
                    }
                }

                $item_serial_no = ItemSerial::where("serial_no", $data['item_serial_no'])->first();

                if (!$item_serial_no)
                {
                    throw new UserException("Wrong Serial No.", UserException::ERROR_USER_DATA_WRONG);
                }

                if (!$item_serial_no->item_brand_id)
                {
                    throw new UserException("Serial No. have no make", UserException::ERROR_USER_DATA_WRONG);
                }

                DB::beginTransaction();

                if (!$item_serial_no->is_in)
                {
                    throw new UserException("Serial No. is already Out", UserException::ERROR_USER_DATA_WRONG);
                }

                StockItem::changeQty($item_serial_no->branch_id, $item_serial_no->bin_id, $item_serial_no->item_id, $item_serial_no->item_brand_id, -1 * $item_serial_no->package_qty);

                $item_serial_no->markOut();

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

        $builder->with([
            "branch", "bin", "item", "item.rootItemCategory", "make"
        ]);

        $records = $builder->get();

        foreach($records as $k => $record)
        {
            $record->afterFindForCsv();
        }

        $records = $records->toArray();

        d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $csv_records[] = [
                'ID' => $record['id'],
                'Category' => $record['item']['root_item_category']['display_name'] ?? "",
                $field_labels['item_id'] => $record['item']['sku'] ?? "",
                $field_labels['brand_id'] => $record['make']['display_name'] ?? "",
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
