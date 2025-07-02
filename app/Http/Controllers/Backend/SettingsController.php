<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Models\AjaxLog;
use App\Models\AppMenu;
use App\Models\AppMenuRelatedLink;
use App\Models\BaseModel;
use App\Models\BaseModelInterface;
use App\Models\BaseMongoModel;
use App\Models\CacheLog;
use App\Models\City;
use App\Models\CommandLog;
use App\Models\EmailLog;
use App\Models\Employee;
use App\Models\Enquiry;
use App\Models\FieldSetting;
use App\Models\PageSetting;
use App\Models\Role;
use App\Models\RoleRouteName;
use App\Models\RouteName;
use App\Models\Setting;
use App\Models\SqlLog;
use App\Models\Type;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class SettingsController extends BackendController
{
    public String $routePrefix = "setting";

    protected $modelClass = Setting::class;

    public function general(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $settingModel = new Setting();

            //d($data["data"]['text']); exit;
            foreach ($data["data"]['text'] as $key => $value) {

                if (is_null($value) || strlen(trim($value)) == 0) {
                    $value = "0";
                }

                $settingModel->insertOrUpdate([
                    "name" => $key,
                    "value" => $value,
                ]);
            }

            Session::flash('success', 'Settings has been saved');
        }

        $form = [
            'url' => route($this->routePrefix . '.general'),
            'method' => 'POST',
        ];

        $record_list = Setting::pluck("value", "name")->toArray();

        //d($record_list); exit;

        $this->setForView(compact('form', 'record_list'));

        return $this->view(__FUNCTION__);
    }

    public function cache_clear()
    {
        Artisan::call('cache:clear');

        return "Cache Cleared";
    }

    public function field_setting($type = "")
    {
        $models = LaravelExtend::getListOfModels();

        $model_list = [];

        $temp_exception_list = [
            AjaxLog::class,
            EmailLog::class,
            SqlLog::class,
            CacheLog::class,
            CommandLog::class,
            BaseModel::class,
            BaseModelInterface::class,
            AppMenu::class,
            AppMenuRelatedLink::class,
            BaseMongoModel::class,
            RouteName::class,
            Setting::class,
            RoleRouteName::class,
            Role::class,
            UserRole::class,
            Type::class,
            FieldSetting::class,
            PageSetting::class,
            Employee::class,
            City::class,
            Enquiry::class,
        ];

        $exception_list = [];

        foreach ($temp_exception_list as $k => $class_name) {
            $exception_list[] = str_class_name_without_namespace($class_name);
        }

        foreach ($models as $model_name => $ref_model_class) {
            if (!in_array($model_name, $exception_list)) {
                $model_list[$model_name] = str_class_name_to_human_text($model_name);
            }
        }

        $search['type'] = $type;

        $request = request();

        if ($type) {

            if (is_string($type)) {
                $type = ucwords($type);
            }

            if (!isset($models[$type])) {
                abort(ACTION_NOT_PROCEED, "Invalid type : $type");
            }

            $ref_model_class = "App\\Models\\" . $type;

            $ref_model = $ref_model_class::first();

            if (!$ref_model) {
                $ref_model = new $ref_model_class();
            }

            $fields = $ref_model->getFieldLabels();

            // d($fields, true);

            $model = FieldSetting::where("type", $type)->first();
            if (!$model) {
                $model = new FieldSetting();
            }

            if ($request->isMethod("post")) {

                $data = $request->all();

                try {

                    $save_data = [
                        "type" => $type,
                        "field_labels" => $data['field_labels'],
                    ];

                    if (!$model->insertOrUpdate($save_data)) {
                        throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
                    }

                    return back()->with("success", "Record save successfully");
                } catch (Exception $ex) {

                    $this->custom_errors = array_merge_recursive($this->custom_errors, $model->custom_errors);

                    return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", $this->custom_errors);
                }
            } else {
            }

            $form = [
                'url' => route($this->routePrefix . '.' . __FUNCTION__),
                'method' => 'POST',
            ];

            $this->setForView(compact("form", "model", "fields"));
        }

        $this->setForView(compact("model_list", "search"));

        return $this->view(__FUNCTION__);
    }

    public function page_setting($type = "")
    {
        $exception_list = [];

        $search['type'] = $type;

        $request = request();

        if ($type) {

            if (is_string($type)) {
                $type = ucwords($type);
            }

            dd($type);

            // d($fields, true);

            $model = FieldSetting::where("type", $type)->first();
            if (!$model) {
                $model = new FieldSetting();
            }

            if ($request->isMethod("post")) {

                $data = $request->all();

                try {

                    $save_data = [
                        "type" => $type,
                        "field_labels" => $data['field_labels'],
                    ];

                    if (!$model->insertOrUpdate($save_data)) {
                        throw new UserException("Fail To save", UserException::ERROR_LARAVEL_FAIL);
                    }

                    return back()->with("success", "Record save successfully");
                } catch (Exception $ex) {

                    $this->custom_errors = array_merge_recursive($this->custom_errors, $model->custom_errors);

                    return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", $this->custom_errors);
                }
            } else {
            }

            $form = [
                'url' => route($this->routePrefix . '.' . __FUNCTION__),
                'method' => 'POST',
            ];

            $this->setForView(compact("form", "model", "fields"));
        }

        $this->setForView(compact("search"));

        return $this->view(__FUNCTION__);
    }
}
