<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ArrayHelper;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Models\Branch;
use App\Models\FieldSetting;
use App\Models\Holiday;
use App\Models\HolidayDate;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class HolidayController extends BackendController
{
    public String $routePrefix = "holiday";

    protected $modelClass = Holiday::class;

    protected Holiday | null $model = null;

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

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "name", "type" => "string"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::where($conditions);
        $builder->with([
            "holidayDate", 
            "branch"
        ]);

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

        $branch_list = Branch::getList("id", "display_name", [], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("branch_list", "field_labels"));
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
            'name' => ["required", "max:80"],
            'for_all_branches' => ["required"],
            "dates" => [],
            "branches" => []
        ];
    }

    private function _common_validation_messages()
    {
        return [
        ];
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
            // d($validatedData, true);

            $save_data = $validatedData;
            unset($save_data['branches']);
            unset($save_data['dates']);

            $this->model = $this->modelClass::create($save_data);

            $this->_saveOthers($validatedData);

            DB::commit();

            return $this->afterSave($this->model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['dates']))
        {
            $validatedData['dates'] = explode(",", $validatedData['dates']);

            $this->model->holidayDate()->delete();

            foreach($validatedData['dates'] as $date)
            {
                $arr = [
                    "date" => DateUtility::getDate($date, DateUtility::DATE_FORMAT)
                ];

                $child_model = new HolidayDate($arr);

                $this->model->holidayDate()->save($child_model);
            }
        }

        if (isset($validatedData['branches']))
        {
            $branches = Branch::find($validatedData['branches']);
            $this->model->branch()->sync($branches);
        }
    }

    public function edit($id)
    {
        $this->model = $model = $this->modelClass::with(["holidayDate", "holidayBranch"])->findOrFail($id);

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
            d($validatedData, true);
            $model->fill($validatedData);

            $model->save();

            DB::commit();

            return $this->afterSave($model);
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
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
}
