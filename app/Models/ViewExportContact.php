<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Database\Eloquent\Builder;

class ViewExportContact extends BaseModel
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

    public function country()
    {
        return $this->belongsTo(ExpCountry::class, "country_id");
    }

    public function state()
    {
        return $this->belongsTo(ExpState::class, "state_id");
    }

    public function product()
    {
        return $this->belongsToMany(Product::class, ExpLeadProduct::class, "exp_lead_id");
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
                "ref_id" => $arr["ref_id"],
                "type" => $arr["type"],
                "name" =>  $arr[$value_field] ?? null
            ];
        }

        return $list;
    }
}
