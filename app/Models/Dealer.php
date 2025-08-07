<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Dealer extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["type", "name"];

    protected static array $list_fields = ["id", "name", "type"];

    const TYPE_DEALER = "DEALER";
    const TYPE_TRADER = "TRADER";

    public static array $child_model_class = [
        // TaskParty::class => [
        //     "foreignKey" => ["party_id"],
        //     "preventDelete" => true
        // ]
    ];

    protected static array $fields_containg_tags = ["description"];

    public string $field_for_create_tag = "display_name";

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        $type_list = $this->getTypeList();

        $name .= " (" . get_value_from_list($type_list, $this->type) . ")";

        return $name;
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->parseTagsForModuleLinking();
    }

    public function documentFile()
    {
        return $this->belongsToMany(DocumentFile::class, DealerFile::class);
    }

    public function dealerFile()
    {
        return $this->hasMany(DealerFile::class, 'dealer_id');
    }

    public function terms()
    {
        return $this->hasMany(DealerTerm::class, 'dealer_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "mobile" => [Rule::unique($this->connection . "." . $this->getTable(), "mobile")],
        ];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        // d([$field => $this->id]);
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }

    public static function getTypeList()
    {
        $list = [
            self::TYPE_DEALER => "Dealer",
            self::TYPE_TRADER=> "Trader",
        ];

        return $list;
    }

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();
        $labels['terms'] = "Terms and Conditions";
        $labels['dealerFile'] = "Files";

        return $labels;
    }
}
