<?php

namespace App\Models;

<<<<<<< HEAD
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends BaseModel
{
    use HasFactory;

    public const LABELS = [
        "company_name" => "Company Name",
=======
class Setting extends BaseMongoModel
{
    public const LABELS = [
        "company_name" => "Company Name",
        "company_tag_line" => "Company Tag Line",
>>>>>>> base_v3
        "company_email" => "Company Email",
        "company_address" => "Company Address",
        "company_gst_no" => "Company GST No.",
        "export_csv_max_record_count" => "Export CSV Max Record Count",
        "item_sku_pattern" => "Item SKU Pattern",
<<<<<<< HEAD
        "purchase_qty_to_bill_qty_variation_per" => "Purchase Qty To Bill Qty Variation Percenatge",
        "purchase_rate_to_bill_rate_variation_per" => "Purchase Rate To Bill Rate Variation Percenatge",
        "sale_qty_to_bill_qty_variation_per" => "Sale Qty To Bill Qty Variation Percenatge",
        "sale_rate_to_bill_rate_variation_per" => "Sale Rate To Bill Rate Variation Percenatge",
=======
>>>>>>> base_v3
    ];

    /**
     * name of table fields which uniquly identify the record
     */
<<<<<<< HEAD
    protected static Array $unique_fields = ["name"];
=======
    protected static array $unique_fields = ["name"];
>>>>>>> base_v3

    /**
     * set extra relationship array to overcome problem of accidential delete
     * this variable used in Controller.php -> delete()
     */
    public static function getValue($name)
    {
        $record = self::where("name", $name)->first();

<<<<<<< HEAD
        if ($record && strlen($record->value) > 0)
        {
=======
        if ($record && strlen($record->value) > 0) {
>>>>>>> base_v3
            return $record->value;
        }

        return null;
    }

<<<<<<< HEAD
    public static function getValueOrFail($name) : string
    {
        $value = self::getValue($name);

        if (is_null($value))
        {
=======
    public static function getValueOrFail($name): string
    {
        $value = self::getValue($name);

        if (is_null($value)) {
>>>>>>> base_v3
            $label = self::LABELS[$name] ?? $name;
            abort(ACTION_NOT_PROCEED, "Setting : $label is not set yet");
        }

        return $value;
    }

<<<<<<< HEAD
    public function getValueOfList(array $names)
    {
        $list = self::whereIn("name", $names)->pluck("value", "name")->toArray();

        foreach($names as $name)
        {
            if (!isset($list[$name]))
            {
=======
    public static function getValueOfList(array $names)
    {
        $list = self::whereIn("name", $names)->pluck("value", "name")->toArray();

        foreach ($names as $name) {
            if (!isset($list[$name])) {
>>>>>>> base_v3
                $label = self::LABELS[$name] ?? $name;
                abort(ACTION_NOT_PROCEED, "Setting : $label is not set yet");
            }
        }

        return $list;
    }
}
