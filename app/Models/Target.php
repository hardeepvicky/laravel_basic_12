<?php

namespace App\Models;

use App\Helpers\DateUtility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Target extends BaseModel
{
    protected $casts = [
        'end_date' => 'date:' . DateUtility::DATE_OUT_FORMAT,
    ];

    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["name"];

    protected static array $list_fields = ["id", "name"];

    public static array $child_model_class = [
        TaskTarget::class => [
            "foreignKey" => "target_id",
            "preventDelete" => true
        ],
    ];

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        return $name;
    }

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function task()
    {
        return $this->belongsToMany(Task::class, TaskTarget::class);
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")  ],
        ];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        // d([$field => $this->id]);
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }
}
