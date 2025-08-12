<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Carrier extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["name"];

    protected static array $list_fields = ["id", "name"];

    public static array $child_model_class = [
        CarrierFile::class => [
            "foreignKey" => ["carrier_id"],
            "preventDelete" => false
        ],
        CarrierTerm::class => [
            "foreignKey" => ["carrier_id"],
            "preventDelete" => false
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
        return $this->belongsToMany(DocumentFile::class, CarrierFile::class);
    }

    public function carrierFile()
    {
        return $this->hasMany(CarrierFile::class, 'carrier_id');
    }

    public function terms()
    {
        return $this->hasMany(CarrierTerm::class, 'carrier_id');
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "mobile" => [Rule::unique($this->connection . "." . $this->getTable(), "mobile")],
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
        $labels['terms'] = "Terms and Conditions";
        $labels['carrierFile'] = "Files";

        return $labels;
    }
}
