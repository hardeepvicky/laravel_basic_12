<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Campaign extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static array $unique_fields = ["name"];

    protected static array $list_fields = ["id", "name"];

    public static array $child_model_class = [
        CampaignFile::class => [
            "foreignKey" => ["campaign_id"],
            "preventDelete" => false
        ],
        Lead::class => [
            "foreignKey" => ["campaign_id"],
            "preventDelete" => true
        ],
    ];

    protected static array $fields_containg_tags = ["description"];

    public string $field_for_create_tag = "display_name";

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        return $name;
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->parseTagsForModuleLinking();
    }

    public function documentFile()
    {
        return $this->belongsToMany(DocumentFile::class, CampaignFile::class);
    }

    public function campaignFile()
    {
        return $this->hasMany(CampaignFile::class, 'campaign_id');
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
        $labels['campaignFile'] = "Files";

        return $labels;
    }
}
