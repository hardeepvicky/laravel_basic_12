<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use App\Helpers\LaravelExtend;
use App\Models\DropDownValue;
use App\Models\ExpCity;
use App\Models\ExpCompetitor;
use App\Models\ExpCountry;
use App\Models\ExpCustomer;
use App\Models\ExpDealer;
use App\Models\ExpDealerAddress;
use App\Models\ExpLead;
use App\Models\ExpLeadAddress;
use App\Models\ExpLeadHistory;
use App\Models\ExpLeadProduct;
use App\Models\ExpParty;
use App\Models\ExpPartyAddress;
use App\Models\ExpState;
use App\Models\FieldSetting;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Type;
use App\Models\User;
use App\Models\ViewExportContact;
use App\Rules\MobileNumber;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ExportContactController extends BackendController
{
    public String $routePrefix = "export-contact";

    protected $modelClass = ViewExportContact::class;

    protected $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        // d(LaravelExtend::getQueryLog("", -5), true);

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
            ["field" => "country_id", "type" => "int"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "city_id", "type" => "int"],
        ]);

        $product_conditions = $this->getConditions($cache_key . ".2", [
            ["field" => "products.id", "type" => "int", "view_field" => "product_id"],
        ]);

        // d($conditions);

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
            "country",
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

        $state_conditions = $city_conditions = ["is_active" => 1];
        $city_list = $state_list = [];

        if (isset($condition_list['country_id']))
        {
            $state_conditions["country_id"] = $condition_list['country_id'];
            $state_list = ExpState::getList("id", "display_name", $state_conditions);
        }

        if (isset($condition_list['state_id']))
        {
            $city_conditions["state_id"] = $condition_list['state_id'];
            $city_list = ExpCity::getList("id", "display_name", $city_conditions);
        }


        $this->setForView(compact("state_list", "city_list"));

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

        $exp_lead = new Lead();

        $address_type_list = ExpLeadAddress::getTypeList();

        $type_conditions = [
            'type' => Type::getType($exp_lead, "source_type")
        ];

        $source_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType($exp_lead, "deal_type")
        ];

        $deal_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $type_conditions = [
            'type' => Type::getType($exp_lead, "status_type")
        ];

        $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $status_type_list_for_form = array_diff($status_type_list, ["Converted"]);

        $source_list = DropDownValue::getList("id", "display_name", ["type" => DropDownValue::LEAD_SOURCE], "name");

        $dealer_list = ExpDealer::getList();

        $product_list = Product::getList();

        $user_list = User::getList("id", "display_name", ["type" => User::TYPE_INTERNAL], "person_name");

        $country_list = ExpCountry::getListCache();

        $type_conditions = [
            'type' => Type::getType(new ExpLeadHistory(), "action_type")
        ];

        $action_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $history_response_list = DropDownValue::getList("id", "display_name", ["type" =>  DropDownValue::LEAD_HISTORY_RESPONSE], "list_display_order");

        $field_labels = FieldSetting::getDynamicFields(ExpLead::class);

        $address_field_labels = FieldSetting::getDynamicFields(ExpLeadAddress::class);

        $product_field_labels = FieldSetting::getDynamicFields(ExpLeadProduct::class);

        $this->setForView(compact(
            "type_list",
            "address_type_list", "source_type_list", "deal_type_list", "status_type_list", "status_type_list_for_form",
            "source_list", "dealer_list", "product_list",
            "user_list", "action_type_list", "history_response_list", "country_list",
            "field_labels", "address_field_labels", "product_field_labels"
        ));

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

        $model->assign_to_user_id = LaravelExtend::getAuthUserId();

        $this->setForView(compact("model", 'form'));

        $this->_set_date_for_add_and_edit();

        return $this->view("form");
    }

    private function _set_date_for_add_and_edit()
    {
    }

    private function _common_validation_rules()
    {
        return [
            "type" => ["required"],
            "name" => ["required"],
            "mobile" => ["required"],
            "mobile2" => ["nullable"],
            "country_id" => ["required"],
            "state_id" => ["required"],
            "city_id" => ["required"],
            "assign_to_user_id" => ["required"],
            "status_type" => ["required"],
            "source_type" => ["nullable"],
            "source_id" => ["nullable"],
            "deal_type" => ["required"],
            "dealer_id" => [],
            "remarks" => ["nullable"],
            "addresses" => [],
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

        $model_class = null;

        switch($request->get('type'))
        {
            case "lead":
            case "customer":
                $model_class = ExpLead::class;
                break;            
            case "party":
                $model_class = ExpParty::class;
                break;
            case "dealer":
                $model_class = ExpDealer::class;
                break;
            case "competitor":
                $model_class = ExpCompetitor::class;
                break;

            default:
                throw new UserException("Invalid Type, type not found in switch case");
        }

        $model = new $model_class();

        $rules = LaravelExtend::merge_validation_rules($rules, $model->getUniqueValidationRules($request));

        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // d($validatedData, true);
            $save_data = $this->_beforeSave($validatedData);
            unset($save_data['type']);
            unset($save_data['addresses']);

            $save_data['assign_to_user_id'] = LaravelExtend::getAuthUserId();

            $this->model = $model_class::create($save_data);

            switch($request->get('type'))
            {
                case "customer":
                    $save_data['exp_lead_id'] = $this->model->id;
                    ExpCustomer::create($save_data);

                    $this->model->is_customer = 1;
                    $this->model->save();
                    break;
            }

            $this->_saveOthers($validatedData);

            if (LaravelExtend::$errors) {
                throw new UserException("There are some errors", UserException::ERROR_USER_DATA_WRONG);
            }

            DB::commit();

            $save_and_close = (bool) request("save_and_close", false);

            if ($save_and_close)
            {
                return redirect()->to(get_current_path_url([
                    'last_insert_id' => $this->model->id,
                    "last_insert_type" => "lead"
                ]));
            }

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', get_exception_display_message($ex))->with("custom_errors", LaravelExtend::$errors);
        }
    }

    private function _beforeSave($validatedData)
    {
        return $validatedData;
    }

    private function _saveOthers($validatedData)
    {
        unset($validatedData['addresses']['(sr-id)']);

        if (isset($validatedData['addresses']))
        {
            switch($validatedData['type'])
            {
                case "lead":
                case "customer":
                    $child_model_class = ExpLeadAddress::class;
                    break;
                case "party":
                    $child_model_class = ExpPartyAddress::class;
                    break;
                case "dealer":
                    $child_model_class = ExpDealerAddress::class;
                    break;
                case "competitor":
                    return true;

                default:
                    throw new UserException("Invalid Type, type not found in switch case");
            }

            $base_rules = [
                "type" => ["required"],
                "addr1" => ["required"],
                "addr2" => ["nullable"],
                "pincode" => ["required"],
                "is_primary" => ""
            ];

            $child_model = new $child_model_class();

            $existing_id_list = $this->model->address()->pluck("id", "id")->toArray();

            $display_order = 0;

            foreach($validatedData['addresses'] as $address)
            {
                switch($validatedData['type'])
                {
                    case "lead":
                    case "customer":
                        $address['exp_lead_id'] = $this->model->id;                    
                        break;
                    case "party":
                        $address['exp_party_id'] = $this->model->id;
                        break;
                    case "dealer":
                        $address['exp_dealer_id'] = $this->model->id;
                        break;
                    case "competitor":
                        $address['exp_competitor_id'] = $this->model->id;
                        break;

                    default:
                        throw new UserException("Invalid Type, type not found in switch case");
                }


                $rules = LaravelExtend::merge_validation_rules($base_rules, $child_model->getUniqueValidationRules($address));

                $validator = Validator::make($address, $rules);

                if ($validator->fails())
                {
                    foreach($validator->errors()->all() as $error_msg)
                    {
                        LaravelExtend::addError("", $error_msg, "Addresses");
                    }
                    continue;
                }

                $display_order++;
                if ($address['id'])
                {
                    $child_model = $child_model_class::find($address['id']);

                    if (!$child_model)
                    {
                        $child_model = new $child_model_class();
                    }
                    else
                    {
                        unset($existing_id_list[$address['id']]);
                    }
                }
                else
                {
                    $child_model = new $child_model_class();
                }

                $address['list_display_order'] = $display_order;

                $child_model->fill($address);

                $this->model->address()->save($child_model);
            }

            if ($existing_id_list)
            {
                $existing_records = $this->model->address()->where("id", $existing_id_list)->get();

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

    public function ajax_get_list()
    {
        $response = ["status" => 1, "data" => []];

        $contact_list = $this->modelClass::getList();

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

                    $route_prefix = "export-" . $record->type;

                    $row['url'] = route($route_prefix . ".edit", [$record->ref_id]);

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
