<?php

namespace App\Http\Controllers\Backend;

use App\Models\Lead;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class CustomerController extends LeadController
{
    public String $routePrefix = "customer";

    protected $modelClass = Lead::class;

    protected Lead | null $model = null;

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
            ["field" => "status_type", "type" => "string"],
            ["field" => "name", "type" => "string"],
            ["field" => "mobile", "type" => "string"],
            ["field" => "source_type", "type" => "string"],
            ["field" => "source_id", "type" => "int"],
            ["field" => "deal_type", "type" => "string"],
            ["field" => "dealer_id", "type" => "int"],
            ["field" => "state_id", "type" => "int"],
            ["field" => "district_id", "type" => "int"],
        ]);

        $builder = $this->modelClass::where($conditions);
        $builder->where("is_customer", 1);

        $builder->with([
            "state", "district", "city",
            "address" => function($q){
                $q->orderBy("list_display_order");
            },
            "documentFile"
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
        if (!parent::beforeViewRender())
        {
            return false;
        }

        $this->viewPrefix = "backend.Lead";
    }

    public function convert_to_lead($lead_id)
    {
        $lead = $this->modelClass::findOrFail($lead_id);

        $lead->is_customer = 0;
        $lead->status_type = Lead::STATUS_CONVERTED;

        $lead->save();

        return redirect()->route("lead.index")->with('success', "Convert to Lead Successfully");
    }
}
