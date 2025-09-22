<?php

namespace App\Models;

use App\Helpers\DateUtility;

class ProductionTargetComplete extends BaseModel
{
    protected $appends = [
    ];

    protected $casts = [
        'target_date' => 'date:' . DateUtility::DATE_OUT_FORMAT,
    ];

    protected static array $unique_fields = [];

    protected static array $list_fields = ["id", "name"];

    public static array $child_model_class = [        
    ];
    
    public function productionTarget()
    {
        return $this->belongsTo(ProductionTarget::class, "production_target_id");
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, "complete_by_employee_id");
    }

    public function afterSave()
    {
        $this->load("productionTarget");

        $this->productionTarget->increment('complete_qty', $this->qty);

        return parent::afterSave();
    }

    public function beforeDelete() 
    {
        $this->load("productionTarget");

        $this->productionTarget->decrement('complete_qty', $this->qty);

        return parent::beforeDelete();
    }
}
