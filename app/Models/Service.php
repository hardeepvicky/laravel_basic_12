<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Service extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected static array $unique_fields = ["ticket_no"];

    protected static array $list_fields = ["id", "ticket_no"];

    public static array $child_model_class = [
        ServiceIssue::class => [
            "foreignKey" => ["service_id"],
            "preventDelete" => false
        ],
    ];

    const ISSUE_TYPE_REGULAR="regular";
    const ISSUE_TYPE_CUSTOM="custom";

    public function afterFindForCsv()
    {
        parent::afterFind();
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->ticket_no;

        return $name;
    }

    public function serviceIssue()
    {
        return $this->hasMany(ServiceIssue::class, "service_id");
    }

    public function productSerial()
    {
        return $this->belongsTo(ProductSerial::class, "product_serial_id");
    }

    public function contact()
    {
        return $this->belongsTo(ViewContact::class, "contact_id");
    }

    public function exportContact()
    {
        return $this->belongsTo(ViewExportContact::class, "export_contact_id");
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "name" => [Rule::unique($this->connection . "." . $this->getTable(), "name")->where("product_category_id", $request->get("product_category_id"))],
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
        return [
            self::ISSUE_TYPE_REGULAR => "Regular",
            self::ISSUE_TYPE_CUSTOM => "Custom",
        ];
    }
}
