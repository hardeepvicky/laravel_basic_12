<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ServiceIssue extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected static array $unique_fields = ["name"];

    protected static array $list_fields = ["id", "name"];

    public static array $child_model_class = [        
    ];

    public function afterFindForCsv()
    {
        parent::afterFind();
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

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function issue()
    {
        return $this->belongsTo(ProductIssue::class, "issue_id");
    }

    public function issueSolution()
    {
        return $this->belongsTo(ProductIssueSolution::class, "issue_solution_id");
    }

    public function getUniqueValidationRules(array $validateData)
    {
        $field_rules = [
        ];

        return $field_rules;
    }
}
