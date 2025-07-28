<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class EmailTemplate extends BaseModel
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

    public function documentFile()
    {
        return $this->belongsToMany(DocumentFile::class, EmailTemplateFile::class);
    }

    public function emailTemplateFile()
    {
        return $this->hasMany(EmailTemplateFile::class, 'email_template_id');
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")],
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

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();

        $labels['emailTemplateFile'] = "Files";

        return $labels;
    }
}
