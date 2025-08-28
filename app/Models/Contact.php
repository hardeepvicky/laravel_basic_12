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

    public static function getRecordsForChooser($builder, $sort) : array
    {
        $records = $builder->with([
            "lead", 
            "lead.city", 
            "customer", 
            "customer.city", 
            "party", 
            "party.city", 
            "dealer", 
            "dealer.city", 
            "competitor",
            "competitor.city",
        ])->get();

        self::getRefListFromUnionOfTables();

        d(LaravelExtend::getQueryLog("", -3));

        $ret_records = [];

        foreach($records as $record)
        {
            $row = [
                "id" => $record->id,
                "ref_id" => $record->ref_id,
                "type" => $record->type,
            ];

            switch($record->type)
            {
                case "lead":
                    $name = ucfirst($record->type) . " >> " . $record->lead->name . " >> " . $record->lead->mobile . " >> " . $record->lead->city->getDisplayName();
                    $row['name'] = $record->lead->name;
                    $row['display_name'] = $name;
                    $row['created_at'] = $record->lead->created_at;
                    $row['updated_at'] = $record->lead->updated_at;
                    break;
                case "customer":
                    $name = ucfirst($record->type) . " >> " . $record->customer->name . " >> " . $record->customer->mobile . " >> " . $record->customer->city->getDisplayName();
                    $row['name'] = $record->customer->name;
                    $row['display_name'] = $name;
                    $row['created_at'] = $record->customer->created_at;
                    $row['updated_at'] = $record->customer->updated_at;
                    break;
                case "party":
                    $name = ucfirst($record->type) . " >> " . $record->party->name . " >> " . $record->party->mobile . " >> " . $record->party->city->getDisplayName();
                    $row['name'] = $record->party->name;
                    $row['display_name'] = $name;
                    $row['created_at'] = $record->party->created_at;
                    $row['updated_at'] = $record->party->updated_at;
                    break;
                case "dealer":
                    $name = ucfirst($record->type) . " >> " . $record->dealer->name . " >> " . $record->dealer->mobile . " >> " . $record->dealer->city->getDisplayName();
                    $row['name'] = $record->dealer->name;
                    $row['display_name'] = $name;
                    $row['created_at'] = $record->dealer->created_at;
                    $row['updated_at'] = $record->dealer->updated_at;
                    break;
                case "competitor":
                    $name = ucfirst($record->type) . " >> " . $record->competitor->name . " >> " . $record->competitor->mobile . " >> " . $record->competitor->city->getDisplayName();
                    $row['name'] = $record->competitor->name;
                    $row['display_name'] = $name;
                    $row['created_at'] = $record->dealer->created_at;
                    $row['updated_at'] = $record->dealer->updated_at;
                    break;
                
                default:
                    throw new UserException("Invalid type : $record->type");
            }

            $ret_records[] = $row;
        }

        if ($sort && isset($sort['sort_by']) && isset($sort['sort_dir']))
        {
            if ($sort['sort_by'] == "id")
            {
                usort($ret_records, function($a, $b) use($sort) {
                    if (strtoupper($sort['sort_dir']) == "ASC")
                    {
                        return $a['ref_id'] <=> $b['ref_id']; 
                    }
                    else
                    {
                        return $b['ref_id'] <=> $a['ref_id']; 
                    }
                });
            }

            if ($sort['sort_by'] == "name")
            {
                usort($ret_records, function($a, $b) use($sort) {
                    if (strtoupper($sort['sort_dir']) == "ASC")
                    {
                        return strcmp($a['name'], $b['name']);  
                    }
                    else
                    {
                        return strcmp($b['name'], $a['name']);  
                    }
                });
            }

            if ($sort['sort_by'] == "created_at")
            {
                usort($ret_records, function($a, $b) use($sort) {
                    if (strtoupper($sort['sort_dir']) == "ASC")
                    {
                        return strtotime($a['created_at']) <=> strtotime($b['created_at']);
                    }
                    else
                    {
                        return strtotime($b['created_at']) <=> strtotime($a['created_at']);
                    }
                });
            }

            if ($sort['sort_by'] == "updated_at")
            {
                usort($ret_records, function($a, $b) use($sort) {
                    if (strtoupper($sort['sort_dir']) == "ASC")
                    {
                        return strtotime($a['updated_at']) <=> strtotime($b['updated_at']);
                    }
                    else
                    {
                        return strtotime($b['updated_at']) <=> strtotime($a['updated_at']);
                    }
                });
            }

        }

        return $ret_records;
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

        static::insertOrUpdate($save, $is_insert, $is_update, true);
    }
}
