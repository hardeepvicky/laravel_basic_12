<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Models\Country;
use App\Models\FieldSetting;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\UserAllowIp;
use App\Models\UserCategory;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends BackendController
{
    public String $routePrefix = "user";

    protected $modelClass = User::class;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

        $role_list = Role::getListCache();

        $user_category_list = UserCategory::getListCache();

        $this->setForView(compact("records", "role_list", "user_category_list"));

        return $this->viewIndex(__FUNCTION__);
    }

    protected function beforeViewRender()
    {
        $type_list = User::TYPE_LIST;

        $country_list = Country::getListCache();

        $my_country = Country::getMyCountry();

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $user_allow_ip_field_labels = FieldSetting::getDynamicFields("UserAllowIp");

        $this->setForView(compact("type_list", "country_list", "my_country", "field_labels", "user_allow_ip_field_labels"));

        return parent::beforeViewRender();
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "name", "type" => "string"],
            ["field" => "email", "type" => "string"],
            ["field" => "mobile", "type" => "string"],
            ["field" => "is_active", "type" => "int"],
            ["field" => "type", "type" => "string_equal"],
            ["field" => "user_category_id", "type" => "int"],
            ["field" => "country_id", "type" => "int"],
        ]);

        $role_condition = $this->getConditions($cache_key . ".2", [
            ["field" => "role_id", "type" => "int"],
        ], true);

        $builder = $this->modelClass::where($conditions)->whereHas(
            'userRole',
            function ($q) use ($role_condition) {
                if (isset($role_condition['role_id'])) {
                    $q->where("role_id", $role_condition['role_id']);
                }
            }
        );

        $builder->with(["userCategory", "userAllowIp"]);

        if ($apply_sort) {
            $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

            $clear_cache = request('is_sort_clear', false);

            $sort_params = $this->getRequestData($cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort_params['sort_by'], $sort_params['sort_dir']);
        }

        return $builder;
    }

    public function create()
    {
        $this->beforeCreate();
        $model = new $this->modelClass();
        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $is_add_form = true;

        $my_country = Country::getMyCountry();

        $model->country_id = $my_country->id;

        $this->_set_for_create_and_edit($model);
        $this->setForView(compact("model", 'form', "is_add_form", "my_country"));
        return $this->view("form");
    }

    private function _set_for_create_and_edit($model)
    {
        $conditions = [];

        if ($model && $model->userRole) {
            $exist_role_ids = $model->userRole->pluck("role_id")->toArray();
            $conditions['or_id'] = $exist_role_ids;
        }
        $role_list = Role::getList("id", "display_name", $conditions);

        $this->setForView(compact("role_list"));
    }

    private function _common_validation_rules()
    {
        return [
            'name' => ['required'],
            'person_name' => ['nullable'],
            'email' => ['required'],
            'type' => ['required'],
            'user_category_id' => ['required'],
            'country_id' => ['required'],
            'mobile' => ['nullable'],
            'roles' => ["required"],
            'allow_ip' => [],
            "dont_send_email" => "",
            "profile_image" => "",
            "is_active" => "",
        ];
    }

    private function _common_validation_messages()
    {
        return [];
    }

    public function store(Request $request)
    {

        $rules = $this->_common_validation_rules();
        $messages = $this->_common_validation_messages();

        $model = new $this->modelClass();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        $rules = array_merge_recursive($rules, [
            'password' => 'required|min:2',
            'password_confirm' => ['required', 'same:password'],
        ]);

        // dd($rules);

        $messages = array_merge_recursive($messages, [
            'confirm_password.same' => 'Confirm password is not matched with password',
        ]);

        // dd("before validate");

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();
        try {

            $validatedData = $this->_saveProfilePhoto($model, $validatedData);

            $validatedData['created_via'] = User::CREATED_VIA_MANUAL_ADD;

            $save_data = $validatedData;

            unset($save_data['roles']);
            unset($save_data['allow_ip']);

            $model->fill($save_data);
            $model->setPassword($save_data['password']);

            if (!$model->save()) {
                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($model, $validatedData);

            if ($this->custom_errors) {
                throw new Exception("Fail to Save");
            }

            if ($validatedData['dont_send_email']) {
                $model->is_active = true;
                $model->save();
            }

            DB::commit();
            $this->saveSqlLog();
            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();
            $this->saveSqlLog();
            return back()->withInput()->with('fail', $ex->getMessage())->with("custom_errors", $this->custom_errors);
        }
    }

    public function edit($id)
    {
        $model = $this->modelClass::with(["userRole", "userAllowIp"])->findOrFail($id);
        $model->afterFind();

        $this->beforeEdit($model);

        if (!$model->country_id) {
            $my_country = Country::getMyCountry();
            $model->country_id = $my_country->id;
        }

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $is_add_form = false;

        $allow_ip = $model->userAllowIp->toArray();

        $this->_set_for_create_and_edit($model);

        $this->setForView(compact("model", "form", "is_add_form", "allow_ip"));

        return $this->view("form");
    }

    public function update($id, Request $request)
    {
        // dd($request->all());
        $model = $this->modelClass::with(["userRole", "userAllowIp"])->findOrFail($id);

        $this->beforeEdit($model);

        $rules = $this->_common_validation_rules();
        $messages = $this->_common_validation_messages();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        // dd($rules);

        $messages = array_merge($messages, [
            'email.email' => 'Email must be valid email address.',
            'email.unique' => 'Email already exist',
        ]);

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $validatedData = $this->_saveProfilePhoto($model, $validatedData);

            // d($validatedData, true);

            $save_data = $validatedData;
            unset($save_data['roles']);
            unset($save_data['allow_ip']);

            $model->fill($save_data);
            if (!$model->save()) {
                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
            }

            $this->_saveOthers($model, $validatedData);

            if ($this->custom_errors) {
                throw new Exception("Fail to Save");
            }

            DB::commit();

            $this->saveSqlLog();

            return $this->afterSave($model);
        } catch (\Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->with('fail', $ex->getMessage())->withInput()->with("custom_errors", $this->custom_errors);
        }
    }

    private function _saveProfilePhoto($model, array $validatedData)
    {
        $validatedData['profile_image'] = trim($validatedData['profile_image']);
        if ($validatedData['profile_image']) {
            $ext = pathinfo($validatedData['profile_image'], PATHINFO_EXTENSION);

            $disk = $model->getStorageDisk();
            $path = $model->getStoragePath();
            if ($model->id) {
                $filename = $model->id . "." . $ext;
            } else {
                $filename = $model->getNextId() . "." . $ext;
            }

            $dest = $disk->path($path . $filename);

            $new_file = FileUtility::move($validatedData['profile_image'], $dest, TRUE);

            if ($new_file === false) {
                throw new UserException("Fail To move file from temp folder to users folder");
            }

            $new_file = pathinfo($new_file, PATHINFO_BASENAME);

            $validatedData['profile_image'] = $path . $new_file;
        } else {
            unset($validatedData['profile_image']);
        }

        return $validatedData;
    }


    private function _saveOthers(User $model, $validatedData)
    {
        $model->assignRoles($validatedData['roles']);

        unset($validatedData['allow_ip']['(sr-id)']);

        if (isset($validatedData['allow_ip'])) {

            $model->userAllowIp()->delete();

            $rules = [
                "ip" => ["required"],
                "description" => ["nullable", "max:255"]
            ];

            $allowIpModel = new UserAllowIp();

            $messages = [
                "ip.unique" => "IP Address is already assigned to other user"
            ];

            foreach ($validatedData['allow_ip'] as $record) {
                try {

                    $new_rules = LaravelExtend::merge_validation_rules($rules, $allowIpModel->getUniqueValidationRules($record));

                    $validator = Validator::make($record, $new_rules, $messages);

                    if ($validator->fails()) {
                        $error_msgs = $validator->messages()->all();
                        $this->addError("", $error_msgs, "Allow IP");
                    } else {
                        unset($record['id']);
                        $model->userAllowIp()->create($record);
                    }
                } catch (Exception $ex) {
                    $this->addError("", $ex->getMessage(), "Allow IP");
                }
            }
        }
    }


    public function destroy($id)
    {
        abort(ACTION_NOT_PROCEED, "Because many tables contain created_by and updated_by field, which refer to user, can not delete");
    }

    public function change_password(Request $request)
    {
        if ($request->isMethod("post")) {
            $validatedData = $request->validate([
                'old_password' => ['required', function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth()->user()->password)) {
                        $fail('The old password is incorrect.');
                    }
                }],
                'new_password' => ['required', 'string', 'min:2'],
                'confirm_password' => ['required', 'same:new_password'],
            ]);

            $user = auth()->user();

            $user->setPassword($validatedData['new_password']);
            if (!$user->save()) {
                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
            }

            return back()->with('success', 'Password changed successfully!');
        }

        return $this->view(__FUNCTION__);
    }
}
