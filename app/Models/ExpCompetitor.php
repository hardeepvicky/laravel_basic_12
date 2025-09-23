<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ExpCompetitor extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["mobile"];

    protected static array $list_fields = ["id", "name", "alias", "mobile", "city_id"];

    public static array $child_model_class = [
        ExpCompetitorAddress::class => [
            "foreignKey" => ["exp_competitor_id"],
            "preventDelete" => false
        ],
        ExpCompetitorFile::class => [
            "foreignKey" => ["exp_competitor_id"],
            "preventDelete" => false
        ],
        ExpCompetitorLink::class => [
            "foreignKey" => ["exp_competitor_id"],
            "preventDelete" => false
        ],
        ExpCompetitorProductCategory::class => [
            "foreignKey" => ["exp_competitor_id"],
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
        ExportContact::createContact("competitor", $this->id);

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
        return $this->belongsToMany(DocumentFile::class, ExpCompetitorFile::class);
    }

    public function productCategory()
    {
        return $this->belongsToMany(ProductCategory::class, ExpCompetitorProductCategory::class);
    }

    public function competitorProductCategory()
    {
        return $this->hasMany(ExpCompetitorProductCategory::class, 'exp_competitor_id');
    }

    public function files()
    {
        return $this->hasMany(ExpCompetitorFile::class, 'exp_competitor_id');
    }

    public function address()
    {
        return $this->hasMany(ExpCompetitorAddress::class, 'exp_competitor_id');
    }

    public function links()
    {
        return $this->hasMany(ExpCompetitorLink::class, 'exp_competitor_id');
    }

    public function state()
    {
        return $this->belongsTo(ExpState::class);
    }

    public function country()
    {
        return $this->belongsTo(ExpCountry::class);
    }

    public function city()
    {
        return $this->belongsTo(ExpCity::class);
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
