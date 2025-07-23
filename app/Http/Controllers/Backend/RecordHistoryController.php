<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\LaravelExtend;
use App\Models\BaseModelInterface;
use App\Models\FieldSetting;
use App\Models\RecordHistory;
use Illuminate\Support\Facades\Route;

class RecordHistoryController extends BackendController
{
    public String $routePrefix = "change-history";

    protected $modelClass = RecordHistory::class;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $conditions = $this->getConditions($cache_key_prefix, [
            ["field" => "id", "type" => "", "cache" => false],
            ["field" => "action", "type" => "string_equal"],
            ["field" => "model_class", "type" => "string_equal"],
            ["field" => "model_id", "type" => "int"],
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],
            ["field" => "created_by", "type" => "int"],            
        ]);

        $builder = RecordHistory::where($conditions);

        $cache_key_for_sort = $cache_key_prefix . "-extra-params";

        $clear_cache = request('is_sort_clear', false);

        $sort_params = $this->getRequestData($cache_key_for_sort, [
            ["key" => "sort_by", "default" => "created_at"],
            ["key" => "sort_dir", "default" => "DESC"],
        ], $clear_cache);

        $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    protected function beforeViewRender()
    {
        if (!parent::beforeViewRender()) {
            return false;
        }

        $index_table_info_options = [
            "show_as" => "celec_datetime"
        ];

        $models = LaravelExtend::getListOfModels();

        $model_list = [];

        foreach($models as $model_name => $model_class)
        {
            $main_model = new $model_class();

            if ($model_name == "User" || $main_model instanceof BaseModelInterface)
            {
                $model_list[$model_name] = str_class_name_to_human_text($model_name);
            }
            else
            {
                continue;
            }
        }

        $action_list = RecordHistory::getActionList();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact("index_table_info_options", "model_list", "action_list", "field_labels"));

        return true;
    }

    protected function parseConditionValue($value, String $type)
    {
        return LaravelExtend::getMongodbParseCondition($value, $type);
    }
}
