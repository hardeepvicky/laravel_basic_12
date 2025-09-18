<?php

namespace App\Models;

class ItemSubDepartment extends BaseModel
{
    protected static Array $unique_fields = ["item_id", "sub_dept_id"];   
    
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function subDept()
    {
        return $this->belongsTo(SubDepartment::class);
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

            case "sub_dept_id":
                $ret_array = [
                    $field => $this->subDept?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
