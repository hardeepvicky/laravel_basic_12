<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use Illuminate\Support\Facades\DB;

class Contact extends BaseModel
{
    protected static array $unique_fields = ["type", "ref_id"];

    protected static array $list_fields = ["id", "type", "ref_id"];

    public static array $child_model_class = [
        
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'ref_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'ref_id');
    }
    public function party()
    {
        return $this->belongsTo(Party::class, 'ref_id');
    }
    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'ref_id');
    }
    public function competitor()
    {
        return $this->belongsTo(Competitor::class, 'ref_id');
    }
    
    public static function getListCache(String $key_field = "id", String $value_field = "display_name", $order_by = "name", $order_dir = "ASC")
    {
        throw new UserException("Data Comes from multiple tables, no can not cache");
    }

    public static function getRefListFromUnionOfTables()
    {
        $leads = Lead::select('id', DB::raw("'lead' as type"), 'name', 'mobile', 'city_id');
        $customers = Customer::select('id', DB::raw("'customer' as type"), 'name', 'mobile', 'city_id');
        $parties = Party::select('id', DB::raw("'party' as type"), 'name', 'mobile', 'city_id')->where("is_active", 1);
        $dealers = Dealer::select('id', DB::raw("'dealer' as type"), 'name', 'mobile', 'city_id')->where("is_active", 1);
        $competitors = Competitor::select('id', DB::raw("'competitor' as type"), 'name', 'mobile', 'city_id')->where("is_active", 1);

        $contacts = DB::query()->fromSub(
            $leads->union($customers)->union($parties)->union($dealers)->union($competitors)
            ,'contacts'
        )->join('cities', 'cities.id', '=', 'contacts.city_id')
        ->select('contacts.*', 'cities.name as city_name')
        ->orderBy('contacts.name', 'ASC')
        ->get();

        $list = [];

        foreach($contacts->toArray() as $contact)
        {
            $name = ucfirst($contact->type) . " >> " . $contact->name . " >> " . $contact->mobile . " >> " . $contact->city_name;

            $list[] = [
                "ref_id" => $contact->id,
                "name" => $name,
                "type" => $contact->type,
            ];
        }

        return $list;
    }

    public static function createContact($type, $ref_id)
    {
        $save = [
            "type" => $type,
            "ref_id" => $ref_id
        ];

        return static::insertOrUpdate($save, $is_insert, $is_update, true);
    }
}
