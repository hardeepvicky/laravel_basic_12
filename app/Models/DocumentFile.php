<?php

namespace App\Models;

use App\Helpers\FileUtility;
use App\Helpers\Util;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class DocumentFile extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    public static array $child_model_class = [
        // Product::class => [
        //     "foreignKey" => "product_category_id",
        //     "preventDelete" => true
        // ],
        // ProductCategoryAttribute::class => [
        //     "foreignKey" => "product_category_id",
        //     "preventDelete" => false
        // ],
    ];

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function categoryAttribute()
    {
        return $this->hasMany(ProductCategoryAttribute::class);
    }

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
        $field_rules = [
        ];

        return $field_rules;
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->font_awesome_icon = FileUtility::getFontAwesomeOnType($this->type);
    }
}
