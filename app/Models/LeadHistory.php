<?php

namespace App\Models;

class LeadHistory extends BaseModel
{
    protected static Array $unique_fields = [];

    const ACTION_TYPE_CALL = "CALL";
    const ACTION_TYPE_SEND_SMS = "SEND_SMS";
    const ACTION_TYPE_SEND_WHATSAPP = "SEND_WHATSAPP";
    const ACTION_TYPE_SEND_EMAIL = "SEND_EMAIL";
    const ACTION_TYPE_LEAD_TRANSFER = "LEAD_TRANSFER";
    const ACTION_TYPE_DISPATCH = "DISPATCH";

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "lead_id":
                $ret_array = [
                    $field => $this->lead?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }

    public static function getCodeList(String $field)
    {
        $class_info = get_class_info(static::class);

        switch ($field) {
            case "action_type":

                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "ACTION_TYPE_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;

                break;
        }
    }
}
