<?php

namespace App\Http\Controllers\Backend;

use App\Models\AppCategory;
use App\Models\AppCity;
use App\Models\AppCountry;
use App\Models\AppLeadType;
use App\Models\AppSource;
use App\Models\AppStatus;
use App\Models\Enquiry;
use Illuminate\Support\Facades\Route;

class EnquiryController extends BackendController
{
    public String $routePrefix = "enquiries";

    protected $modelClass = Enquiry::class;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

        $records = $this->getPaginagteRecords($builder, $cache_key_prefix);

        $country_list = AppCountry::getListCache();

        $source_list = AppSource::getList();

        $status_list = AppStatus::getList();

        $type_list = AppLeadType::getList();

        $category_list = AppCategory::getList();

        $country_state_city_list = AppCity::getCountryStateCityList();

        $this->setForView(compact("records", "country_state_city_list", "country_list", "category_list", "source_list", "type_list", "status_list"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _getBuildeForIndex($cache_key_prefix, $apply_sort = true)
    {
        $cache_key = $cache_key_prefix . "-index";

        $conditions = $this->getConditions($cache_key, [
            ["field" => "name", "type" => "string"],
            ["field" => "mobile", "type" => "string"],
            ["field" => "email", "type" => "string"],
            ["field" => "country_id", "type" => "string_equal"],
            ["field" => "state_id", "type" => "string_equal"],
            ["field" => "city_id", "type" => "string_equal"],
            ["field" => "source_id", "type" => "string_equal"],
            ["field" => "status_id", "type" => "string_equal"],
            ["field" => "type_id", "type" => "string_equal"],
            ["field" => "category_id", "type" => "string_equal"],
        ]);


        $builder = $this->modelClass::where($conditions);

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
}
