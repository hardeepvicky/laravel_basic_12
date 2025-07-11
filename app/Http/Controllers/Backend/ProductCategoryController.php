<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ArrayHelper;
use App\Helpers\LaravelExtend;
use App\Models\Department;
use App\Models\FieldSetting;
use App\Models\ProductCategory;
use App\Models\ProductCategoryAttribute;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ProductCategoryController extends BackendController
{
    public String $routePrefix = "product-category";

    protected $modelClass = ProductCategory::class;

    protected ProductCategory | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d(LaravelExtend::getQueryLog("", -3));

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "parent_id", "type" => "int", "view_field" => "parent_id"],
            ["field" => "is_find_nested_child", "type" => "bool", "view_field" => "is_find_nested_child"],
            ["field" => "name", "type" => "string", "view_field" => "name"],
            ["field" => "is_last_node", "type" => "", "view_field" => "is_last_node"],
            ["field" => "is_active", "type" => "", "view_field" => "is_active"],
        ]);

        $builder = $this->modelClass::query();

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        if (
            (isset($condition_list["parent_id"]) && $condition_list["parent_id"])
            && (isset($condition_list["is_find_nested_child"]) && $condition_list["is_find_nested_child"])
        )
        {
            $child_id_list = $this->modelClass::getChildIdList([$condition_list["parent_id"]]);

            if ($child_id_list)
            {
                $builder->whereIn("id", $child_id_list);
                $conditions = $array_helper->unsetValues($conditions, ["parent_id"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);
            }
        }
        
        $conditions = $array_helper->unsetValues($conditions, ["is_find_nested_child"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

        if($conditions)
        {
            $builder = $builder->where($conditions);
        }

        if ($apply_sort)
        {
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

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $category_attribute_field_labels = FieldSetting::getDynamicFields(ProductCategoryAttribute::class);

        $qb = $this->modelClass::query()->where("is_last_node", 0);

        $tree_list = $this->modelClass::getTreeList($qb);

        $product_category_attribute = new ProductCategoryAttribute();

        $type_conditions = [];
        $type_conditions['type'] = Type::getType($product_category_attribute, "input_type");

        foreach($this->model->categoryAttribute as $categoryAttribute)
        {
            if ($categoryAttribute->input_type) {
                $saved_type_id = Type::getIdFromCode($type_conditions['type'], $categoryAttribute->input_type);

                if ($saved_type_id) {
                    $type_conditions['or_id'][] = $saved_type_id;
                }
            }
        }

        $input_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $this->setForView(compact("field_labels", "category_attribute_field_labels", "input_type_list", "tree_list"));

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
            "name" => ["required"],
            "parent_id" => ["nullable"],
            "short_name" => ["nullable"],
            "list_display_order" => "",
            "is_last_node" => "",
            "is_active" => "",
            "productCategoryAttribute" => [],
        ];
    }

    private function _common_validation_messages()
    {
        return [
        ];
    }

    public function store(Request $request)
    {
        $this->model = new $this->modelClass();

        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try
        {
            $save_data = $validatedData;

            unset($save_data['categoryAttribute']);

            $this->model = $this->modelClass::create($save_data);

            $this->_saveOthers($validatedData);

            d(LaravelExtend::getQueryLog("", -6));
            
            if (LaravelExtend::$errors) { 
                throw new Exception("Fail to Save");
            }
            
            DB::commit();

            return $this->afterSave($this->model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();
            
            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers(array $validatedData)
    {
        unset($validatedData['productCategoryAttribute']['(sr-id)']);

        // d($validatedData, true);

        if (isset($validatedData['productCategoryAttribute']))
        {
            $rules = [
                "name" => ["required"],
                "input_type" => ["required"],
                "dropdown_type" => ["required"],
                "is_active" => "",
                "list_display_order" => "",
            ];

            $child_model = new ProductCategoryAttribute();

            $validatedData['product_category_id'] = $this->model->id;

            $rules = LaravelExtend::merge_validation_rules($rules, $child_model->getUniqueValidationRules($validatedData));

            $display_order = 0;

            foreach($validatedData['productCategoryAttribute'] as $categoryAttribute)
            {
                $display_order++;
                if ($categoryAttribute['id'])
                {
                    $child_model = ProductCategoryAttribute::find($categoryAttribute['id']);

                    if (!$child_model)
                    {
                        $child_model = new ProductCategoryAttribute();
                    }
                }
                else
                {
                    $child_model = new ProductCategoryAttribute();
                }

                $categoryAttribute['list_display_order'] = $display_order;

                $child_model->fill($categoryAttribute);

                $this->model->categoryAttribute()->save($child_model);
            }
        }
    }

    public function edit($id)
    {
        $model = $this->model = $this->modelClass::with(["categoryAttribute"])->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $productCategoryAttribute = $this->model->categoryAttribute?->toArray() ?? [];

        $this->setForView(compact("model", "form", "productCategoryAttribute"));

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        $model = $this->model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $this->model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            
            $save_data = $validatedData;

            unset($save_data['categoryAttribute']);

            $this->model->fill($save_data);

            $this->model->save();

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) { 
                throw new Exception("Fail to Save");
            }

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", LaravelExtend::$errors);
        }
    }
}
