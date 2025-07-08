<?php

namespace App\Http\Controllers\Backend;

use App\Models\City;
use App\Models\State;
use App\Models\Tag;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class TagsController extends BackendController
{
    public String $routePrefix = "tag";

    protected $modelClass = Tag::class;
    protected Tag | null $model = null;

    public function index()
    {
        $cache_key_prefix = Route::currentRouteName();

        $builder = $this->_getBuildeForIndex($cache_key_prefix);

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
            ["field" => "name", "type" => "string"],
            ["field" => "type", "type" => ""],
        ]);

        $builder = $this->modelClass::where($conditions);
        $builder->with("tagLink");

        if ($apply_sort)
        {
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

    public function ajax_get_list(Request $request)
    {
        $response = ["status" => 1, "data" => []];

        try
        {
            $search_term = $request->get("query");

            if ($search_term && strlen(trim($search_term)) > 0)
            {
                $search_term = trim($search_term);
                
                $records = $this->modelClass::where("search_term", 'LIKE', '%' . $search_term . '%')
                            ->orderBy("foreign_model", "DESC")
                            ->orderBy("search_term", "ASC")
                            ->limit(20)
                            ->get();

                if  ($records && $records->count() > 0)
                {
                    foreach($records as $record)
                    {
                        $row = [];

                        if ($record->main_model)
                        {
                            $row['display'] = $record->search_term;
                            $row['name'] = "#" . $record->main_model . ".id:" . $record->main_id . " (" . $record->name . ")";
                        }
                        else
                        {
                            $row['display'] = $record->search_term;
                            $row['name'] = "#" . $row['name'];
                        }

                        $response['data'][] = $row;
                    }
                }     
            }
        }
        catch(Exception $ex)
        {
            // Log::error($ex->getMessage());
            throw $ex;
        }

        return $this->responseJson($response);
    }
}
