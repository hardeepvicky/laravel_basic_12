<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Models\Contact;
use App\Models\FieldSetting;
use App\Models\ViewContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ContactController extends BackendController
{
    public String $routePrefix = "contact";

    protected $modelClass = Contact::class;

    protected Contact | null $model = null;
    
    public function chooser()
    {
        $this->paginationLimit = request('pagination_limit', 100);

        $cache_key_prefix = Route::currentRouteName();

        $type_list = [
            'lead' => "Lead", 
            'customer' => "Customer", 
            'party' => "Party", 
            'dealer' => "Dealer", 
            'competitor' => "Competitor"
        ];

        $conditions = $this->getConditions($cache_key_prefix, [
            ["field" => "type", "type" => "string_equal"],
            ["field" => "name", "type" => "string"],
            ["field" => "mobile", "type" => "string"],
            ["field" => "mobile2", "type" => "string"],
            ["field" => "email", "type" => "string"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "district_id", "type" => "int"],
            ["field" => "city_id", "type" => "int"],
        ]);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = ViewContact::query();
        $builder->where($conditions);

        $this->model = new $this->modelClass;

        $cache_key_for_sort = $cache_key_prefix . "-index-extra-params";

        $sort_by_list = [
            "id" => "ID",
            "name" => "Name",
            "created_at" => "Created",
            "updated_at" => "Updated",
        ];

        $sort_dir_list = [
            "ASC" => "Ascending",
            "DESC" => "Descending"
        ];

        $this->setForView(compact("sort_by_list", "sort_dir_list"));

        $clear_cache = request('is_sort_clear', false);
        $value = request('value', false);

        if ($value) {
            $builder->orderByRaw('FIELD(id,' . $value . ') DESC');
            $builder->orderBy("id", "DESC");

            $this->setForView([
                "selected_value" => $value,
                "selected_id_list" => explode(",", $value)
            ]);

            $sort = [
                'sort_by' => 'id',
                'sort_dir' => 'DESC'
            ];

        } else {
            $sort = $this->getSortBy($this->model, $cache_key_for_sort, [
                ["key" => "sort_by", "default" => "id"],
                ["key" => "sort_dir", "default" => "DESC"],
            ], $clear_cache);

            $builder->orderBy($sort['sort_by'], $sort['sort_dir']);
        }

        $this->setForView($sort);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);
        
        $index_table_id = request("index_table_id", "index_table");
        $modal_id = request("modal_id", "");
        $multiple = (bool) request("multiple", false);

        $this->setForView(compact("records", "type_list", "index_table_id", "modal_id", "multiple"));

        return $this->viewIndex(__FUNCTION__);
    }

    protected function beforeViewRender()
    {
        return parent::beforeViewRender();
    }
}
