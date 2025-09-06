<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Competitor extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["mobile"];

    protected static array $list_fields = ["id", "name", "alias", "mobile", "city_id"];

    public static array $child_model_class = [
        CompetitorAddress::class => [
            "foreignKey" => ["competitor_id"],
            "preventDelete" => false
        ],
        CompetitorFile::class => [
            "foreignKey" => ["competitor_id"],
            "preventDelete" => false
        ],
        CompetitorLink::class => [
            "foreignKey" => ["competitor_id"],
            "preventDelete" => false
        ], 
        CompetitorProductCategory::class => [
            "foreignKey" => ["competitor_id"],
            "preventDelete" => false
        ],       
    ];

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        if ($this->alias)
        {
            $name .= " (" . $this->alias . ")";
        }

        if ($this->mobile)
        {
            $name .= " >> " . $this->mobile;
        }

        return $name;
    }

    public function afterSave()
    {
        Contact::createContact("competitor", $this->id);

        return parent::afterSave();
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->parseTagsForModuleLinking();
    }

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function documentFile()
    {
        return $this->belongsToMany(DocumentFile::class, CompetitorFile::class);
    }

    public function productCategory()
    {
        return $this->belongsToMany(ProductCategory::class, CompetitorProductCategory::class);
    }

    public function competitorProductCategory()
    {
        return $this->hasMany(CompetitorProductCategory::class, 'competitor_id');
    }

    public function files()
    {
        return $this->hasMany(CompetitorFile::class, 'competitor_id');
    }

    public function address()
    {
        return $this->hasMany(CompetitorAddress::class, 'competitor_id');
    }

    public function links()
    {
        return $this->hasMany(CompetitorLink::class, 'competitor_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
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

        $labels['competitorProductCategory'] = "Product Category";

        return $labels;
    }
}
