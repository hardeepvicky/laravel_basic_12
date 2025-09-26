<?php

namespace App\Models;

class ItemSubDepartment extends BaseModel
{
    protected $casts = [
        'sub_dept_id' => 'array',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function dept()
    {
        return $this->belongsTo(Department::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "item_id":
                $ret_array = [
                    $field => $this->item?->getDisplayName()
                ];
                break;

            case "dept_id":
                $ret_array = [
                    $field => $this->dept?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
