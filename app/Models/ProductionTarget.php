<?php

namespace App\Models;

use App\Helpers\DateUtility;
use Illuminate\Http\Request;

class ProductionTarget extends BaseModel
{
    protected $appends = [
    ];

    protected $casts = [
        'target_date' => 'date:' . DateUtility::DATE_OUT_FORMAT,
    ];

    protected static array $unique_fields = [];

    protected static array $list_fields = ["id", "name"];

    public static array $child_model_class = [
        ProductionTargetComplete::class => [
            "foreignKey" => ["production_target_id"],
            "preventDelete" => true
        ],
    ];

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, "responsible_user_id");
    }

    public function productionTarget()
    {
        return $this->hasMany(ProductionTargetComplete::class);
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
        ];

        return $field_rules;
    }
}
