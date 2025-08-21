<?php

namespace App\Models;

use App\Helpers\LaravelExtend;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class AutoSerialPattern extends BaseModel
{
    protected static array $unique_fields = ["type"];

    public static array $child_model_class = [
       
    ];

    public const TYPE_PRODUCT = "PRODUCT";
    public const TYPE_ITEM = "ITEM";

    public function beforeSave()
    {
        if ($this->pattern)
        {
            if (strpos($this->pattern, "{counter}") === false)
            {
                LaravelExtend::addError("pattern", "must have {counter} word");
            }
        }

        parent::beforeSave();
    }

    public function type()
    {
        return $this->belongsTo(DropDownValue::class);
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "type" => [Rule::unique($this->connection . "." . $this->getTable(), "type")],
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

    public static function getTypeList()
    {
        return [
            self::TYPE_PRODUCT => "Product",
            self::TYPE_ITEM => "Item",
        ];
    }
}
