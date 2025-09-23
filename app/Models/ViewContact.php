<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Database\Eloquent\Builder;

class ViewContact extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected static array $list_fields = ["id", "ref_id", "type", "name", "mobile", "city_name"];

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
        return ucfirst($this->type) . " >> " . $this->name . " >> " . $this->city_name;
    }

    public function state()
    {
        return $this->belongsTo(State::class, "state_id");
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, "ref_id");
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, "ref_id");
    }

    public function party()
    {
        return $this->belongsTo(Party::class, "ref_id");
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "ref_id");
    }

    public function competitor()
    {
        return $this->belongsTo(Competitor::class, "ref_id");
    }

    public function product()
    {
        return $this->belongsToMany(Product::class, LeadProduct::class, "lead_id");
    }

    public static function fetchList(Builder $builder, String $key_field, String $value_field)
    {
        $model = new static();
        $table_name  = $model->getTable();

        $list_fields = static::getListFields();
        
        $builder->select($list_fields);

        $records = $builder->get();

        // dd($records->toArray()); exit;

        $list = [];

        foreach ($records as $record) {
            $arr = $record->toArray();

            if ($value_field == "display_name")
            {
                $arr[$value_field] = ucfirst($arr['type']) . " >> " . $arr['name'] . " >> " . $arr['city_name'] . " >> " . $arr['mobile'] ;
            }

            $list[$arr['type'] . "." . $arr[$key_field]] = [
                "id" => $arr["id"],
                "ref_id" => $arr["ref_id"],
                "type" => $arr["type"],
                "name" =>  $arr[$value_field] ?? null
            ];
        }

        return $list;
    }
}
