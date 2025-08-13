<?php

namespace App\Models;

use App\Helpers\LaravelExtend;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class City extends BaseModel
{
    protected static array $unique_fields = ["state_id", "district_id", "name"];

    protected $appends = [
        'display_name',
    ];

    public static array $child_model_class = [
        // Employee::class => [
        //     "foreignKey" => ["current_state_id", "permanent_state_id"],
        //     "preventDelete" => true
        // ]
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        return $name;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("district_id", $request->input("district_id"))],
        ];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "state_id":
                $ret_array = [
                    $field => $this->state?->getDisplayName()
                ];
                break;

            case "district_id":
                $ret_array = [
                    $field => $this->district?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public static function getWithDistrict(array $state_conditions = [], array $district_conditions = [], array $city_conditions = [])
    {
        $data = [];

        $cache_key = "getWithDistrict";
        $cache_key .= "|" . LaravelExtend::getCacheKeyFromConditions($state_conditions, "state");
        $cache_key .= "|" . LaravelExtend::getCacheKeyFromConditions($district_conditions, "district");
        $cache_key .= "|" . LaravelExtend::getCacheKeyFromConditions($city_conditions, "city");

        $data = static::getCache($cache_key);

        // dd($data);

        if (!$data)
        {
            $data = [];

            $records = District::where($district_conditions)
                ->whereHas("state", function ($q) use($state_conditions) {
                    $q->where($state_conditions);
                })
                ->with([
                    "city" => function($q) use($city_conditions){
                        $q->where($city_conditions);
                        $q->orderBy("name", "ASC");
                    }
                ])->orderBy("name", "ASC")->get();
            
            foreach($records as $record)
            {
                $prefix = $record->getDisplayName();

                foreach($record->city as $city)
                {
                    $data[$city->id] = [];
                    $data[$city->id]['id'] = $city->id;
                    $data[$city->id]['name'] = $prefix . " > " . $city->getDisplayName();
                    $data[$city->id]['state_id'] = $city->state_id;
                    $data[$city->id]['district_id'] = $city->district_id;
                }
            }

            $cache_time = 120;
            static::addCache($cache_key, $data, $cache_time);
        }

        return $data;
    }
}
