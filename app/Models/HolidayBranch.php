<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class HolidayBranch extends BaseModel
{
    protected static Array $unique_fields = ["holiday_id", "branch_id"];

    public function holiday()
    {
        return $this->belongsTo(Holiday::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "holiday_id":
                $ret_array = [
                    $field => $this->item?->getDisplayName()
                ];
                break;

            case "branch_id":
                $ret_array = [
                    $field => $this->branch?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
