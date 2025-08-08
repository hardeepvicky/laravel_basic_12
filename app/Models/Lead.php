<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Lead extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = [];

    protected static array $list_fields = ["id", "name"];

    public static array $child_model_class = [
        LeadAddress::class => [
            "foreignKey" => ["lead_id"],
            "preventDelete" => false
        ],
        LeadFile::class => [
            "foreignKey" => ["lead_id"],
            "preventDelete" => false
        ],
    ];

    protected static array $fields_containg_tags = ["description"];

    public string $field_for_create_tag = "display_name";

    public const STATUS_HOT = "HOT";
    public const STATUS_WARM = "WARM";
    public const STATUS_COLD = "COLD";
    public const STATUS_CONVERTED = "CONVERTED";
    public const STATUS_LOST = "LOST";
    public const STATUS_DEAD = "DEAD";
    public const STATUS_FAKE = "FAKE";

    public const SOURCE_TYPE_PAID = "PAID";
    public const SOURCE_TYPE_ORGANIC = "ORGANIC";
    public const SOURCE_TYPE_REFFRENCE = "REFFRENCE";
    public const SOURCE_TYPE_OTHER = "OTHER";

    public const DEAL_TYPE_DIRECT = "DIRECT";
    public const DEAL_TYPE_THROGH_DEALER = "THROGH_DEALER";

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;
        
        return $name;
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->parseTagsForModuleLinking();
    }

    public function documentFile()
    {
        return $this->belongsToMany(DocumentFile::class, LeadFile::class);
    }

    public function leadFile()
    {
        return $this->hasMany(LeadFile::class, 'lead_id');
    }

    public function address()
    {
        return $this->hasMany(LeadAddress::class, 'lead_id');
    }

    public function products()
    {
        return $this->hasMany(LeadProduct::class, 'lead_id');
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

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();
        $labels['leadFile'] = "Files";
        $labels['addresses'] = "Addresses";
        $labels['products'] = "Products";

        return $labels;
    }

    
    public static function getCodeList(String $field)
    {
        $class_info = get_class_info(static::class);


        switch ($field) {
            case "source_type":

                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "SOURCE_TYPE_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;

                break;

            case "status_type":
                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "STATUS_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;
                break;

            case "deal_type":
                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "DEAL_TYPE_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;
                break;
        }
    }
}
