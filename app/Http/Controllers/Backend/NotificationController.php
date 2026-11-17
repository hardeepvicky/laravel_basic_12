<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\LaravelExtend;
use App\Models\Notification;
use App\Models\Type;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Route;

class NotificationController extends BackendController
{
    public String $routePrefix = "notification";

    protected $modelClass = Notification::class;

    protected Notification | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // dd($records->toArray());

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    public function my()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $builder->where("notifiable_id", LaravelExtend::getAuthUserId());

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
            ["field" => "from_user_id", "type" => "int"],
            ["field" => "notifiable_id", "type" => "int", "view_field" => "to_user_id"],
            ["field" => "sub_type", "type" => "string_equal"],
            ["field" => "unread_only", "type" => "int"],
            ["field" => "created_at", "type" => "from_date", "view_field" => "from_date"],
            ["field" => "created_at", "type" => "to_date", "view_field" => "to_date"],            
        ]);

        $builder = $this->modelClass::query();

        $arry_helper = new ArrayHelper($conditions);
        $condition_list = $arry_helper->convertModelSearchConditionToList();

        if (isset($condition_list['unread_only']))
        {
            if ($condition_list['unread_only'])
            {
                $builder->whereNull("read_at");
            }

            foreach($conditions as $k => $arr)
            {
                if ($arr[0] == "unread_only")
                {
                    unset($conditions[$k]);
                }
            }
        }

        // d($conditions, true);

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

        $to_user_list = User::getListCache();

        $from_list = [0 => "System"] + $to_user_list;

        $type_conditions = [];
        $type_conditions['type'] = Type::getType($this->model, "sub_type");
        if ($this->model->sub_type) {
            $saved_type_id = Type::getIdFromCode($type_conditions['type'], $this->model->sub_type);

            if ($saved_type_id) {
                $type_conditions['or_id'] = $saved_type_id;
            }
        }

        $notification_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $this->setForView(compact("from_list", "to_user_list", "notification_type_list"));

        return parent::beforeViewRender();
    }

    public function ajax_get_list()
    {
        $response = ["status" => 1];

        try {

            $pattern_array = [
                ["field" => "name", "type" => "string"],
                ["field" => "product_category_id", "type" => "int"],
            ];

            $conditions = $this->getConditions(null, $pattern_array);

            if (empty($conditions))
            {
                throw new UserException("No any condition applied");
            }

            $qb = $this->modelClass::where($conditions);
            $qb->where("is_active", 1);

            $records = $qb->get();

            $response['data'] = [];

            foreach ($records as $record) {
                $row = [
                    "id" => $record->id,
                    "name" => $record->getDisplayName(),
                ];

                $response['data'][] = $row;
            }
        } catch (Exception $ex) {
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }

    public function ajax_mark_read()
    {
        $response = ["status" => 0, "msg" => "Unknown"];

        try
        {
            $records = $this->modelClass::where("notifiable_id", LaravelExtend::getAuthUserId())->whereNull("read_at")->get();

            foreach($records as $notification)
            {
                $notification->markAsRead();
            }

            $response['status'] = 1;
        }
        catch(Exception $ex)
        {
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }
}
