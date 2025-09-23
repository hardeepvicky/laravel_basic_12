<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class SmsTemplate extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected $casts = [
        'placeholders' => 'array',
    ];

    public static array $child_model_class = [
       
    ];

    protected static array $unique_fields = ["name"];

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [];

        if ($request->has("name"))
        {
            $field_rules['name'] = [Rule::unique($this->connection . "." . $this->getTable(), "name")];
        }

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

    public function getRequiredValidationRules(Request $request)
    {
        $field_rules = [];

        if (!$this->name)
        {
            $field_rules['name'] = ["required"];
        }

        return $field_rules;
    }

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();

        $labels['emailTemplateFile'] = "Files";

        return $labels;
    }

    public function getTemplate(array $placeholder_data) : string
    {
        $template = $this->template;

        $placeholder_values = $this->resolvePlaceholders($placeholder_data);

        foreach($placeholder_values as $placeholder => $value)
        {
            if (is_scalar($value))
            {
                $template = str_replace("@". $placeholder, $value, $template);
            }
        }

        return $template;
    }

    public function resolvePlaceholders(array $placeholder_data) : array
    {
        $default_placeholder_data = $this->getPlaceholderCommonData();

        $placeholder_data = array_merge($default_placeholder_data, $placeholder_data);

        return $placeholder_data;
    }

    public static function getPlaceholderCommonData() : array
    {
        $placeholders = [
            "company.name" => "",            
        ];

        $setting_list = Setting::getValueOfList(["company_name"]);

        $placeholders['company.name'] = $setting_list['company_name'];
        
        return $placeholders;
    }
}


