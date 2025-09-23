<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;

class Designation extends BaseModel
{
    //
=======
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Designation extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["name"];

    protected static array $list_fields = ["id", "name", "short_name"];

    public static array $child_model_class = [
        // Employee::class => [
        //     "foreignKey" => "designation_id",
        //     "preventDelete" => true
        // ],
        // User::class => [
        //     "foreignKey" => "designation_id",
        //     "preventDelete" => true
        // ],
    ];

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->short_name;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")],
            "level" => [Rule::unique($this->connection . "." . $this->getTable(), "level")],
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

    public function getUserIdListBasedOnDesignationLevel()
    {
        $desg_id_list = self::where("level", "<", $this->level)->pluck("id")->toArray();

        $user_id_list = [];

        if ($desg_id_list)
        {
            $user_id_list = User::whereIn("desg_id", $desg_id_list)->pluck("id")->toArray();
        }

        return $user_id_list;
    }

    public function getParentUserIdListBasedOnDesignationLevel()
    {
        $desg_id_list = self::where("level", ">", $this->level)->pluck("id")->toArray();

        $user_id_list = [];

        if ($desg_id_list)
        {
            $user_id_list = User::whereIn("desg_id", $desg_id_list)->pluck("id")->toArray();
        }

        return $user_id_list;
    }
>>>>>>> base_v3
}
