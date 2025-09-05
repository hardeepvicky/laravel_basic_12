<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use Illuminate\Support\Facades\DB;

class ExportContact extends BaseModel
{
    protected static array $unique_fields = ["type", "ref_id"];

    protected static array $list_fields = ["id", "type", "ref_id"];

    public static array $child_model_class = [
        
    ];

    public function lead()
    {
        return $this->belongsTo(ExpLead::class, 'ref_id');
    }
    public function customer()
    {
        return $this->belongsTo(ExpCustomer::class, 'ref_id');
    }
    public function party()
    {
        return $this->belongsTo(Party::class, 'ref_id');
    }
    public function dealer()
    {
        return $this->belongsTo(ExpDealer::class, 'ref_id');
    }
    public function competitor()
    {
        return $this->belongsTo(Competitor::class, 'ref_id');
    }

    public static function createContact($type, $ref_id)
    {
        $save = [
            "type" => $type,
            "ref_id" => $ref_id
        ];

        static::insertOrUpdate($save, $is_insert, $is_update, true);
    }
}
