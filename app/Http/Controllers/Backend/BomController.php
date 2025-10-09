<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\CsvUtility;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\Util;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\FieldSetting;
use App\Models\Item;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class BomController extends BackendController
{
    public String $routePrefix = "bom";

    const TABS = [
        "default" => "Default",
        "item" => "Items",
    ];

    protected $modelClass = Bom::class;
    protected Bom | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->with([
            "item",
            "product",
            "bomItems",
            "bomItems.item",
            "bomItems.item.category",
            "bomItems.item.category.uom",
        ]);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "type", "type" => "string_equal"],
            ["field" => "item_id", "type" => "int"],
            ["field" => "product_id", "type" => "int"],
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

        $item_list = Item::getList("id", "display_name", [], "list_display_order");

        $product_list = Product::getList("id", "display_name", [], "list_display_order");

        $type_list = Bom::getTypeList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $bom_item_field_labels = FieldSetting::getDynamicFields(BomItem::class);

        $this->setForView(compact(
            "item_list", "product_list", "type_list",
            "field_labels", "bom_item_field_labels"
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

        $current_tab = $model->tab_name = array_key_first(self::TABS);

        $this->setForView(compact("model", 'form', 'current_tab'));

        $this->set_data_for_add_edit($model);

        return $this->view("tabs");
    }

    private function set_data_for_add_edit($model)
    {
        $tabs = self::TABS;

        $main_item_list = Item::getList("id", "display_name", [ "can_be_use_for_bom" => 1], "list_display_order");

        $this->setForView(compact("tabs", "main_item_list"));
    }

    private function _common_validation_rules()
    {
        return [
            "type" => ["required"],
            "item_id" => ["nullable"],
            "product_id" => ["nullable"],
            "remarks" => "",
            "tab_name" => "",
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

        $messages = $this->_common_validation_messages();

        $this->model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData, true);

        DB::beginTransaction();

        try {
            $save_data = $validatedData;

            // dd($save_data);

            $this->model->fill($save_data);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            $this->updateUserInfoAfterSave($this->model);

            DB::commit();

            $this->saveSqlLog();

            $next_tab = Util::getNextTab(self::TABS, $validatedData['tab_name'], $is_last_tab);

            return redirect()->route($this->routePrefix . ".edit", ["id" => $this->model->id, "tab" => $next_tab])->with('success', 'Record created successfully');

        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['bomItems']) )
        {
            unset($validatedData['bomItems']['(sr-id)']);

            // d($validatedData, true);

            if (isset($validatedData['bomItems'])) {
                $base_rules = [
                    "item_id" => ["required"],
                    "qty" => ["required"],
                ];

                $child_model = new BomItem();

                $existing_id_list = $this->model->bomItems()->pluck("id", "id")->toArray();

                $validatedData['bom_id'] = $this->model->id;

                $display_order = 0;

                foreach ($validatedData['bomItems'] as $item_bom) {

                    $validator = Validator::make($item_bom, $base_rules);

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $error_msg) {
                            LaravelExtend::addError("", $error_msg, "Items");
                        }
                        continue;
                    }

                    $display_order++;
                    if ($item_bom['id']) {
                        $child_model = BomItem::find($item_bom['id']);

                        if (!$child_model) {
                            $child_model = new BomItem();
                        } else {
                            unset($existing_id_list[$item_bom['id']]);
                        }
                    } else {
                        $child_model = new BomItem();
                    }

                    // dd($item_bom);

                    $child_model->fill($item_bom);

                    $child_model = $this->model->bomItems()->save($child_model);
                }

                if ($existing_id_list) {
                    $existing_records = $this->model->bomItems()->where("id", $existing_id_list)->get();

                    foreach ($existing_records as $existing_record) {
                        if (!$existing_record->safeDelete($existing_record)) {
                            LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                        }
                    }
                }
            }
        }

    }

    public function edit($id, $current_tab = "default")
    {
        $model = $this->model = $this->modelClass::with([
            "bomItems"
        ])->findOrFail($id);

        $this->beforeEdit($this->model);

        $form = [
            'url' => route($this->routePrefix . '.update', [$id, $current_tab]),
            'method' => 'PUT',
        ];

        $this->setForView(compact("model", "form", "current_tab"));

        $this->set_data_for_add_edit($model);

        return $this->view("tabs");
    }

    public function update($id, $current_tab, Request $request)
    {
        $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($this->model);

        switch($current_tab)
        {
            case "default":
                $rules = $this->_common_validation_rules();
                $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));
                $messages = $this->_common_validation_messages();
                break;

            case "item":
                $rules = [
                    "remarks" => [],
                    "bomItems" => [],                    
                ];
                break;
        }


        // d($rules);

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // d($validatedData);

        DB::beginTransaction();

        try {

            $save_data = $validatedData;
            unset($save_data['bomItems']);

            $this->model->fill($save_data);

            if (!$this->model->save()) {
                throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new Exception("There are some errors");
            }

            $this->updateUserInfoAfterSave($this->model);

            DB::commit();

            $this->saveSqlLog();

            $is_last_tab = false;
            $next_tab = Util::getNextTab(self::TABS, $current_tab, $is_last_tab);

            if ($is_last_tab)
            {
                return redirect()->route($this->routePrefix . ".index")->with('success', 'Record updated successfully');
            }
            else
            {
                return redirect()->route($this->routePrefix . ".edit", ["id" => $this->model->id, "tab" => $next_tab])->with('success', 'Record updated successfully');
            }

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

        $builder->with([
            "item",
            "product",
            "bomItems",
            "bomItems.item",
            "bomItems.item.category",
            "bomItems.item.category.uom",
        ]);

        $records = $builder->get();

        $records = $records->toArray();

        // d($records, true);

        $csv_records = [];
        $user_cache_list = User::getListCache();

        $yes_no_list = laravel_constant('yes_no_list');

        $type_list = Bom::getTypeList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        foreach($records as $record)
        {            
            foreach($record['bom_items'] as $bom_item)
            {
                $csv_records[] = [
                    'ID' => $record['id'],
                    $field_labels['type'] => get_value_from_list($type_list, $record['type']),
                    $field_labels['item_id'] => $record['item']['sku'] ?? "",
                    $field_labels['product_id'] => $record['product']['display_name'] ?? "",
                    $field_labels['remarks'] => $record['remarks'] ?? "",

                    'Consume Item' => $bom_item['item']['sku'] ?? "",
                    'Consume qty' => $bom_item['qty'] ?? "",
                    'Consume Unit' => $bom_item['item']['category']['uom']['short_name'] ?? "",
                    
                    
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
