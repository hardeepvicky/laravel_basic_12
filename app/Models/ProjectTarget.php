<?php

namespace App\Models;

use App\Helpers\DateUtility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProjectTarget extends BaseModel
{
    protected $casts = [
        'end_date' => 'date:' . DateUtility::DATE_OUT_FORMAT,
    ];

    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["name"];

    protected static array $list_fields = ["id", "project_id", "name"];

    public static array $child_model_class = [
        TaskProjectTarget::class => [
            "foreignKey" => "project_target_id",
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

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsToMany(Task::class, TaskProjectTarget::class);
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("project_id", $request->get("project_id") ) ],
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
