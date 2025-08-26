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
use App\Models\StockProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
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

        $product_conditions = $this->getConditions($cache_key . ".2", [
            ["field" => "product_category_id", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($product_conditions);
        $category_condition_list = $array_helper->convertModelSearchConditionToList();

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
            "branch", "bin", "product"
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

        if (isset($category_condition_list['product_category_id'])) {
            $product_list = Product::getList("id", "display_name", ["product_category_id" => $category_condition_list['product_category_id']], "list_display_order");
        }

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

        $product_category_records = ProductCategory::getTreeRecords(ProductCategory::query());

        $product_category_list = Util::getTreeListArray($product_category_records, "id", "name", false, false);

        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");

        $make_list = Maker::getList("id", "display_name", [], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("product_category_list", "branch_list", "make_list", "field_labels"));

        return parent::beforeViewRender();
    }

    public function in(Request $request)
    {
        if ($request->isMethod("post"))
        {
            $data = $request->all();

            d($data, true);
        }

        $form = [
            'url' => Route::currentRouteName(),
            'method' => 'POST',
        ];

        $this->setForView(compact("model", 'form'));

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

        d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {
            $csv_records[] = [
                'ID' => $record['id'],
                $field_labels['branch_id'] => $record['branch']['name'] ?? "",
                $field_labels['name'] => $record['name'],
                $field_labels['code'] => $record['code'],
                $field_labels['remarks'] => $record['remarks'] ?? "",

                $field_labels['is_active'] => get_value_from_list($yes_no_list, $record['is_active']),
                $field_labels['list_display_order'] => $record['list_display_order'] ?? "",

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
