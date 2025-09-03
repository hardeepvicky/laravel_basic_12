<?php

namespace App\Models;

class PartyHistory extends BaseModel
{
    protected $casts = [
        'next_followup_date_time' => 'datetime',
    ];

    protected static Array $unique_fields = [];

    protected static array $file_fields = [];

    const ACTION_TYPE_CALL = "CALL";
    const ACTION_TYPE_SEND_SMS = "SEND_SMS";
    const ACTION_TYPE_SEND_WHATSAPP = "SEND_WHATSAPP";
    const ACTION_TYPE_SEND_EMAIL = "SEND_EMAIL";
    const ACTION_TYPE_CHANGE_STATUS = "CHANGE_STATUS";

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function assignTo()
    {
        return $this->belongsTo(User::class, "assign_to_user_id");
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "dealer_id":
                $ret_array = [
                    $field => $this->lead?->getDisplayName()
                ];
                break;

            case "assign_to_user_id":
                $ret_array = [
                    $field => $this->assignTo?->getDisplayName()
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
