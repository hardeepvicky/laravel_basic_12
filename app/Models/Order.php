<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Support\Facades\DB;

class Order extends BaseModel
{
    protected static array $unique_fields = ["type", "ref_id"];

    protected static array $list_fields = ["id", "type", "ref_id"];

    public static array $child_model_class = [
        
    ];

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class, 'ref_id');
    }
    public function exportOrder()
    {
        return $this->belongsTo(ExportOrder::class, 'ref_id');
    }

    public static function getListCache(String $key_field = "id", String $value_field = "display_name", $order_by = "name", $order_dir = "ASC")
    {
        throw new UserException("Data Comes from multiple tables, no can not cache");
    }

    public static function getRefListFromUnionOfTables()
    {
        $sale = SaleOrder::select('id', DB::raw("'sale' as type"), 'order_no', 'order_from_type');
        $export = ExportOrder::select('id', DB::raw("'export' as type"), 'order_no', 'order_from_type');
        
        $contacts = DB::query()->fromSub(
            $sale->union($export)
            ,'orders'
        )
        ->orderBy('orders.id', 'DESC')
        ->get();

        $list = [];

        foreach($contacts->toArray() as $contact)
        {
            $name = ucfirst($contact->order_from_type) . " >> " . $contact->order_no;

            $list[] = [
                "ref_id" => $contact->id,
                "name" => $name,
                "type" => $contact->type,
            ];
        }

        return $list;
    }

    public static function createOrder($type, $ref_id)
    {
        $save = [
            "type" => $type,
            "ref_id" => $ref_id
        ];

        static::insertOrUpdate($save, $is_insert, $is_update, true);
    }
}
