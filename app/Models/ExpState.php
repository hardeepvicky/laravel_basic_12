<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ExpState extends BaseModel
{
    protected static array $unique_fields = ["country_id", "name"];

    protected static array $list_fields = ["id", "name", "iso2"];

    protected $appends = [
        'display_name',
    ];

    public static array $child_model_class = [
        ExpCity::class => [
            "foreignKey" => ["state_id"],
            "preventDelete" => true
        ], 
        ShipOrder::class => [
            "foreignKey" => ["state_id"],
            "preventDelete" => true
        ],
    ];

    public function country()
    {
        return $this->belongsTo(ExpCountry::class);
    }

    public function city()
    {
        return $this->hasMany(ExpCity::class);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        if ($this->iso2)
        {
            $name .= " (" . $this->iso2 . ")";
        }

        return $name;
    }

    public function getShortName()
    {
        if ($this->iso2)
        {
            return $this->iso2;
        }
        
        return $this->name;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("country_id", $request->input("country_id"))],
            "iso2" => [Rule::unique($this->connection . "." . $this->getTable(), "iso2")->where("country_id", $request->input("country_id"))],
        ];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "country_id":
                $ret_array = [
                    $field => $this->country->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public static function createShortName($name)
    {
        if (!$name) {
            return "";
        }

        $str = str_convert_multi_space_to_single_space($name);

        $str = str_replace(["'"], "", $str);
        $str = str_replace("-", " ", $str);

        $words = explode(' ', $str); // Split the string by space

        $first_chars = [];

        $exception_words = ["and", "or", "by"];
        $retain_words = ["al", "ar", "la", "ash", "as", "su"];

        foreach ($words as $k => $word) {

            $char = "";

            $lower_word = strtolower($word);

            foreach ($retain_words as $retain_word) {
                if ($lower_word == $retain_word) {
                    $char = "-" . $retain_word . "-";
                }
            }

            if (in_array($lower_word, $exception_words)) {
                continue;
            }


            if (empty($char)) {
                $char = strtoupper($word[0]);
            }

            $first_chars[$k] = $char;
        }

        $ret_str =  implode("", $first_chars);

        $ret_str = trim($ret_str, "-");

        $ret_str = trim($ret_str);

        return $ret_str;
    }

    public static function getIndianList(String $key_field = "id", String $value_field = "display_name", $conditions = [], $order_by = "name", $order_dir = "asc")
    {
        $my_country = Country::getMyCountry();

        if (!$my_country)
        {
            throw new UserException("My Country : India not found");
        }

        $conditions['country_id'] = $my_country->id;

        return self::getList($key_field, $value_field, $conditions, $order_by, $order_dir);
    }
}
