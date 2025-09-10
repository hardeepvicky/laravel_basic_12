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
        ShipOrder::class => [
            "foreignKey" => ["export_contact_id"],
            "preventDelete" => true
        ],
        ExportOrder::class => [
            "foreignKey" => ["export_contact_id"],
            "preventDelete" => true
        ],
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
        return $this->belongsTo(ExpParty::class, 'ref_id');
    }
    public function dealer()
    {
        return $this->belongsTo(ExpDealer::class, 'ref_id');
    }
    public function competitor()
    {
        return $this->belongsTo(ExpCompetitor::class, 'ref_id');
    }

    public static function getRefListFromUnionOfTables()
    {
        $leads = ExpLead::select('id', DB::raw("'lead' as type"), 'name', 'mobile', 'city_id');
        $customers = ExpCustomer::select('id', DB::raw("'customer' as type"), 'name', 'mobile', 'city_id');
        $parties = ExpParty::select('id', DB::raw("'party' as type"), 'name', 'mobile', 'city_id')->where("is_active", 1);
        $dealers = ExpDealer::select('id', DB::raw("'dealer' as type"), 'name', 'mobile', 'city_id')->where("is_active", 1);
        $competitors = ExpCompetitor::select('id', DB::raw("'competitor' as type"), 'name', 'mobile', 'city_id')->where("is_active", 1);

        $contacts = DB::query()->fromSub(
            $leads->union($customers)->union($parties)->union($dealers)->union($competitors)
            ,'contacts'
        )->join('exp_cities', 'exp_cities.id', '=', 'contacts.city_id')
        ->select('contacts.*', 'exp_cities.name as city_name')
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
