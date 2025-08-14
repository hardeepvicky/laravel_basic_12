<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Models\Carrier;
use App\Models\CarrierTerm;
use App\Models\DocumentFile;
use App\Models\FieldSetting;
use App\Models\State;
use App\Rules\MobileNumber;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class CarrierController extends BackendController
{
    public String $routePrefix = "carrier";

    protected $modelClass = Carrier::class;

    protected Carrier | null $model = null;

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
            ["field" => "name", "type" => "string"],
            ["field" => "mobile", "type" => "string"],
            ["field" => "is_active", "type" => "int"],
        ]);

        $builder = $this->modelClass::where($conditions);

        $builder->with([
            "terms" => function($q){
                $q->orderBy("list_display_order");
            },
            "carrierFile"
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

        return $builder;
    }

    protected function beforeViewRender()
    {
        $state_list = State::getIndianList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $term_field_labels = FieldSetting::getDynamicFields(CarrierTerm::class);

        $this->setForView(compact("state_list", "field_labels", "term_field_labels"));

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
            "email" => ["required"],
            "mobile" => ["required", new MobileNumber],
            "mobile2" => ["nullable", new MobileNumber],
            "phone_no" => ["nullable"],
            "company_name" => ["nullable"],
            "state_id" => ["required"],
            "district_id" => ["required"],
            "city_id" => ["required"],
            "address" => ["nullable"],
            "description" => ["nullable"],
            "terms" => [],
            "carrierFile" => [],
            "list_display_order" => "",
            "is_active" => ""
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

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $this->model = $this->modelClass::create($validatedData);

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new UserException("There are some errors", UserException::ERROR_USER_DATA_WRONG);
            }

            DB::commit();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _saveOthers($validatedData)
    {
        if (isset($validatedData['carrierFile'])) {

            $ids = explode(",", $validatedData['carrierFile']);

            $document_files = DocumentFile::find($ids);

            $this->model->documentFile()->sync($document_files);
        }

        unset($validatedData['terms']['(sr-id)']);

        if (isset($validatedData['terms']))
        {
            $rules = [
                "name" => ["required"],
                "is_active" => ""
            ];

            $child_model = new CarrierTerm();

            $existing_id_list = $this->model->terms()->pluck("id", "id")->toArray();

            $validatedData['carrier_id'] = $this->model->id;

            // $rules = LaravelExtend::merge_validation_rules($rules, $child_model->getUniqueValidationRules($validatedData));

            $display_order = 0;

            foreach($validatedData['terms'] as $term)
            {
                $validator = Validator::make($term, $rules, [
                    "name.required" => "Term is required"
                ]);

                if ($validator->fails())
                {
                    foreach($validator->errors()->all() as $error_msg)
                    {               
                        LaravelExtend::addError("", $error_msg, "Terms");
                    }
                    continue;
                }

                $display_order++;
                if ($term['id'])
                {
                    $child_model = CarrierTerm::find($term['id']);

                    if (!$child_model)
                    {
                        $child_model = new CarrierTerm();
                    }
                    else
                    {
                        unset($existing_id_list[$term['id']]);
                    }
                }
                else
                {
                    $child_model = new CarrierTerm();
                }

                $term['list_display_order'] = $display_order;

                $child_model->fill($term);

                $this->model->terms()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->terms()->where("id", $existing_id_list)->get();

                foreach($existing_records as $existing_record)
                {
                    if (!$existing_record->safeDelete($existing_record))
                    {
                        LaravelExtend::addError($existing_record->name, "Can not delete, bcoz it is asscoicated with other modules.", "Term");
                    }
                }
            }
        }
    }

    public function edit($id)
    {
        $this->model = $model = $this->modelClass::with([
            "terms" => function($q){
                $q->orderBy("list_display_order");
            },
        ])->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $terms = $this->model->terms?->toArray() ?? [];

        $this->setForView(compact("model", "form", "terms"));

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        $this->model = $model = $this->modelClass::findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        // dd($rules);

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // d($validatedData, true);

            $this->model->fill($validatedData);

            $this->model->save();

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new UserException("There are some errors", UserException::ERROR_USER_DATA_WRONG);
            }

            DB::commit();

            return $this->afterSave($this->model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    public function copy($id)
    {
        $this->model = $model = $this->modelClass::with([
            "terms" => function($q){
                $q->orderBy("list_display_order");
            },
        ])->findOrFail($id);

        $this->beforeEdit($model);

        $form = [
            'url' => route($this->routePrefix . '.store', $id),
            'method' => 'POST',
        ];

        $terms = $this->model->terms?->toArray() ?? [];

        $this->setForView(compact("model", "form", "terms"));

        return $this->view("form");
    }
}
