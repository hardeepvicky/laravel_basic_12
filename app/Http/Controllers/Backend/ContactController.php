<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Models\City;
use App\Models\Country;
use App\Models\DropDownValue;
use App\Models\FieldSetting;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\Product;
use App\Models\State;
use App\Models\Type;
use App\Models\ViewContact;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ContactController extends BackendController
{
    public String $routePrefix = "contact";

    protected $modelClass = ViewContact::class;

    protected ViewContact | null $model = null;
    
    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);

        $this->setForView(compact("records"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "id", "type" => "int", "cache" => false],
            ["field" => "type", "type" => "string_equal"],
            ["field" => "status_type", "type" => "string_equal"],
            ["field" => "from_last_followup_date_time", "type" => "date"],
            ["field" => "to_last_followup_date_time", "type" => "date"],
            ["field" => "from_next_followup_date_time", "type" => "date"],
            ["field" => "to_next_followup_date_time", "type" => "date"],
            ["field" => "name", "type" => "string"],
            ["field" => "mobile", "type" => "string"],
            ["field" => "mobile2", "type" => "string"],
            ["field" => "email", "type" => "string"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "district_id", "type" => "int"],
            ["field" => "city_id", "type" => "int"],            
        ]);

        $product_conditions = $this->getConditions($cache_key . ".2", [
            ["field" => "products.id", "type" => "int", "view_field" => "product_id"],
        ]);

        // d($conditions, true);

        $array_helper =  new ArrayHelper($conditions);
        $condition_list = $array_helper->convertModelSearchConditionToList();

        $builder = $this->modelClass::query();

        $this->model = new $this->modelClass();

        if (isset($condition_list['from_last_followup_date_time']))
        {
            $builder->whereDate('last_followup_date_time', ">=", $condition_list['from_last_followup_date_time']);
        }

        if (isset($condition_list['to_last_followup_date_time']))
        {
            $builder->whereDate('last_followup_date_time', "<=", $condition_list['to_last_followup_date_time']);
        }

        if (isset($condition_list['from_next_followup_date_time']))
        {
            $builder->whereDate('next_followup_date_time', ">=", $condition_list['from_next_followup_date_time']);
        }

        if (isset($condition_list['to_next_followup_date_time']))
        {
            $builder->whereDate('next_followup_date_time', "<=", $condition_list['to_next_followup_date_time']);
        }

        $conditions = $array_helper->unsetValues($conditions, ["from_last_followup_date_time", "to_last_followup_date_time", "from_next_followup_date_time", "to_next_followup_date_time"], true, [ArrayHelper::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD]);

        $builder->where($conditions);

        $builder->with([
            "state",
            "product"
        ]);

        if ($product_conditions)
        {
            $builder->whereHas("product", function($q) use ($product_conditions) {
                $q->where($product_conditions);
            });
        }

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

        $my_country = Country::getMyCountry();

        if (!$my_country)
        {
            throw new UserException("Country India not found");
        }

        $state_conditions = [
            ["country_id", $my_country->id],
            ["is_active", 1]
        ];

        $district_conditions = [
            ["is_active", 1]
        ];

        if (isset($condition_list['state_id']))
        {
            $state_conditions[] = ["id", $condition_list['state_id']];
            $district_conditions[] = ["state_id", $condition_list['state_id']];
        }
        
        $district_cities = City::getWithDistrict($state_conditions, $district_conditions, [["is_active", 1]]);
    
        $district_city_collection = collect($district_cities);

        $district_city_list = $district_city_collection->pluck('name', 'id')->toArray();

        $this->setForView(compact("district_city_list"));

        return $builder;
    }

    public function chooser()
    {
        $this->paginationLimit = request('pagination_limit', 100);

        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $this->model = new $this->modelClass;

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d($records->toArray(), true);
        
        $index_table_id = request("index_table_id", "index_table");
        $modal_id = request("modal_id", "");
        $multiple = (bool) request("multiple", false);

        $this->setForView(compact("records", "index_table_id", "modal_id", "multiple"));

        return $this->viewIndex(__FUNCTION__);
    }

    protected function beforeViewRender()
    {
        $type_list = [
            'lead' => "Lead", 
            'customer' => "Customer", 
            'party' => "Party", 
            'dealer' => "Dealer", 
            'competitor' => "Competitor"
        ];

        $state_list = State::getIndianList();

        $lead = new Lead();

        $type_conditions = [
            'type' => Type::getType($lead, "status_type")
        ];

        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType(new LeadHistory(), "action_type")
        ];

        $action_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $response_type_list = DropDownValue::getList("id", "display_name", ["type" =>  DropDownValue::LEAD_HISTORY_RESPONSE], "list_display_order");

        $product_list = Product::getList("id", "model_name", [], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields($this->modelClass);

        $this->setForView(compact(
            "type_list", "state_list", "product_list", "response_type_list", "status_type_list", "action_type_list",
            "field_labels"
        ));

        return parent::beforeViewRender();
    }

    public function ajax_get_list()
    {
        $response = ["status" => 1, "data" => []];

        $contact_list = ViewContact::getList();

        foreach($contact_list as $k => $arr)
        {
            $response['data'][] = [
                'key' => $k,
                "name" => $arr['name']
            ];
        }

        return $this->responseJson($response);
    }

    public function ajax_get_list_for_mobile(Request $request)
    {
        $response = ["status" => 1, "data" => []];

        try
        {
            $search_term = $request->get("query");

            if ($search_term && strlen(trim($search_term)) > 0)
            {
                $search_term = urldecode(trim($search_term));

                $records = $this->modelClass::
                            where("mobile", 'LIKE', '%' . $search_term . '%')
                            ->orWhere("mobile2", 'LIKE', '%' . $search_term . '%')
                            ->orderBy("name", "ASC")
                            ->limit(20)
                            ->get();

            }
            else
            {
                $limit = $request->get("limit");

                if ($limit)
                {
                    $records = $this->modelClass::orderBy("name", "ASC")
                                ->limit($limit)
                                ->get();
                }
            }

            if  (isset($records) && $records->count() > 0)
            {
                foreach($records as $record)
                {
                    $row = [];

                    $row['display'] = $record->getDisplayName();
                    $row['display'] .= " (" . $record->type . ")";

                    $row['url'] = route("contact.edit", [$record]);

                    $response['data'][] = $row;
                }
            }
        }
        catch(Exception $ex)
        {
            Log::error($ex->getMessage());
            throw $ex;
        }

        return $this->responseJson($response);
    }
}
