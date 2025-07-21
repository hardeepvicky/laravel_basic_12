<?php

namespace App\Models;

use App\Helpers\FileUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentFile extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $file_fields = ["file"];

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

    public function getTypeList()
    {
        $list = DB::table($this->getTable())->select('type')->distinct()->get()->pluck("type", "type")->toArray();

        foreach($list as $k => $v)
        {
            $list[$k] = ucwords($v);
        }

        return $list;
    }
}
