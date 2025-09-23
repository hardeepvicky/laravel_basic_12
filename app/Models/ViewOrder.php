<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Database\Eloquent\Builder;

class ViewOrder extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected static array $list_fields = ["id", "ref_id", "type", "order_no", "order_from_type"];

    public static array $child_model_class = [
        
    ];

    public static function getListCache(String $key_field = "id", String $value_field = "display_name", $order_by = "name", $order_dir = "ASC")
    {
        throw new UserException("Data Comes from multiple tables, no can not cache");
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->order_no . " >> " . ucfirst($this->order_from_type);

        $name .= " >> ";

        switch($this->order_from_type)
        {
            case "lead":
                $name .= $this->lead->getDisplayName();
                break;
            case "customer":
                $name .= $this->customer->getDisplayName();
                break;
            case "party":
                $name .= $this->party->getDisplayName();
                break;
            case "dealer":
                $name .= $this->dealer->getDisplayName();
                break;
            case "competitor":
                $name .= $this->competitor->getDisplayName();
                break;
        }

        return $name;
    }

    public function lead()
    {        
        return $this->belongsTo($this->type == "sale" ? Lead::class : ExpLead::class, 'contact_ref_id');
    }
    public function customer()
    {
        return $this->belongsTo($this->type == "sale" ? Customer::class : ExpCustomer::class, 'contact_ref_id');
    }
    public function party()
    {
        return $this->belongsTo($this->type == "sale" ? Party::class : ExpParty::class, 'contact_ref_id');
    }
    public function dealer()
    {
        return $this->belongsTo($this->type == "sale" ? Dealer::class : ExpDealer::class, 'contact_ref_id');
    }
    public function competitor()
    {
        return $this->belongsTo($this->type == "sale" ? Competitor::class : ExpCompetitor::class, 'contact_ref_id');
    }

    public static function fetchList(Builder $builder, string $key_field, string $value_field)
    {
        $builder->eagerLoadRelations(["lead", "customer", "party", "dealer", "competitor"]);
        $builder->whereNotNull("ref_id");
        $records = $builder->get();

        // d($records->toArray()); exit;

        $list = [];

        foreach ($records as $record) {
            $arr = $record->toArray();
            if (isset($arr[$key_field]) && is_scalar($arr[$key_field]))
            {
                $list[$arr[$key_field]] = $arr[$value_field] ?? null;
            }
        }

        return $list;
    }

    public static function getDetailList(Builder $builder, String $key_field, String $value_field)
    {
        $list_fields = static::getListFields();
        
        $builder->select($list_fields);
        $records = $builder->get();

        $list = [];

        foreach ($records as $record) {
            $arr = $record->toArray();

            $list[$arr['type'] . "." . $arr[$key_field]] = [
                "type" => $arr["type"],
                "ref_id" => $arr["ref_id"],
                "order_from_type" => $arr["order_from_type"],
                "contact_ref_id" => $arr["contact_ref_id"],
                "name" =>  $arr[$value_field] ?? null
            ];
        }

        return $list;
    }
}
