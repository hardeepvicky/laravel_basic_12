<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\LaravelExtend;
use App\Models\BaseModel;
use App\Models\BaseModelInterface;
use App\Models\FieldSetting;
use App\Models\RecordHistory;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use MongoDB\Laravel\Eloquent\Model as EloquentModel;

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
            ["field" => "model_display_name", "type" => "string"],
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

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    public function change_history_of_record(Request $request)
    {
        if ($request->isMethod("post"))
        {
            $data = $request->validate([
                "model_class" => "required",
                "model_field" => "required",
                "model_id" => "required",
            ]);

            $this->setForView($data);

            // dd($data);

            $data['model_id'] = (int) $data['model_id'];

            $records = $this->modelClass::where("model_class", $data['model_class'])->where("model_id", $data['model_id'])->get();

            $change_history = [];

            if ($records->count() > 0)
            {
                foreach($records as $record)
                {
                    $record->afterFind();

                    if (isset($record->changes[$data['model_field']]))
                    {
                        $arr = $record->changes[$data['model_field']];
                        $arr['created_by'] = $record->created_by;
                        $arr['created_at_user_tz'] = $record->created_at_user_tz->format(DateUtility::DATETIME_OUT_FORMAT);
                        $change_history[] = $arr;
                    }
                }

                $this->setForView(compact("change_history"));
            }
            else
            {
                Session::flash("fail", "No any record found");
            }
        }

        return $this->view(__FUNCTION__);
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

        $page_title = "Change History";

        $this->setForView(compact("index_table_info_options", "model_list", "action_list", "field_labels", "page_title"));

        return true;
    }

    protected function parseConditionValue($value, String $type, array $extra = [])
    {
        return LaravelExtend::getMongodbParseCondition($value, $type, $extra);
    }

    public function ajax_get_model_fields($model_name)
    {
        $response = ["status" => 1, "data" => []];

        $model_class = "App\\Models\\$model_name";

        try
        {
            if (!class_exists($model_class))
            {
                throw new UserException("$model_name is not exist", UserException::ERROR_USER_DATA_WRONG);
            }

            $model = new $model_class;

            if ($model instanceof Model)
            {
                $table_info = $model->getTableInfo($model->getTable());

                foreach($table_info['columns'] as $field => $arr)
                {
                    $response['data'][] = [
                        "id" => $field,
                        "name" => $field
                    ];
                }
            }
            else if ($model instanceof EloquentModel)
            {
                $record = $model->first();

                foreach($record as $field => $value)
                {
                    $response['data'][] = [
                        "id" => $field,
                        "name" => $field
                    ];
                }
            }
        }
        catch(Exception $ex)
        {
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }
}
