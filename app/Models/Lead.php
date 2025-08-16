<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Lead extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected $casts = [
        'next_meeting_date_time' => 'datetime',
        'snooze_datetime' => 'datetime',
    ];

    protected static array $unique_fields = [];

    protected static array $list_fields = ["id", "name", "mobile"];

    public static array $child_model_class = [
        LeadAddress::class => [
            "foreignKey" => ["lead_id"],
            "preventDelete" => false
        ],
        LeadFile::class => [
            "foreignKey" => ["lead_id"],
            "preventDelete" => false
        ],
        LeadProduct::class => [
            "foreignKey" => ["lead_id"],
            "preventDelete" => false
        ],
        LeadItem::class => [
            "foreignKey" => ["lead_id"],
            "preventDelete" => false
        ],
        LeadHistory::class => [
            "foreignKey" => ["lead_id"],
            "preventDelete" => true
        ],
        TaskLead::class => [
            "foreignKey" => ["lead_id"],
            "preventDelete" => true
        ], 
        TaskCustomer::class => [
            "foreignKey" => ["lead_id"],
            "preventDelete" => true
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

    public const PAYMENT_METHOD_CASH = "CASH";
    public const PAYMENT_METHOD_BANK_TRANSFER = "BANK_TRANSFER";
    public const PAYMENT_METHOD_UPI = "UPI";
    public const PAYMENT_METHOD_CREDIT_CARD = "CREDIT_CARD";
    public const PAYMENT_METHOD_DEBIT_CARD = "DEBIT_CARD";
    

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        $name = $this->name;

        if ($this->mobile)
        {
            $name .= " (M:" . $this->mobile . ")";
        }
        
        return $name;
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->parseTagsForModuleLinking();
    }

    public function afterFindForCsv()
    {
        parent::afterFind();
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

    public function items()
    {
        return $this->hasMany(LeadItem::class, 'lead_id');
    }

    public function history()
    {
        return $this->hasMany(LeadHistory::class, 'lead_id');
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

    public function assignTo()
    {
        return $this->belongsTo(User::class, "assign_to_user_id");
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, "campaign_id");
    }

    public function getRequiedIfRules(Request $request)
    {
        $rules = [];

        if ($request->input("deal_type", false) == self::DEAL_TYPE_THROGH_DEALER) {
            $rules["dealer_id"] = ["required"];
        }

        return $rules;
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

            case "payment_method_type":
                $list = [];

                foreach ($class_info['constants'] as $constant => $label) {
                    if (strpos($constant, "PAYMENT_METHOD_") !== false) {
                        $list[] = $label;
                    }
                }

                return $list;
                break;
        }
    }

    public function changeAssignTo(array $data)
    {
        if ($data['assign_to_user_id'])
        {
            $this->assign_to_user_id = $data['assign_to_user_id'];
            $this->save();

            $data['lead_id'] = $this->id;
            $data['is_lead_transfered'] = 1;
            LeadHistory::create($data);

            $notification_data = [
                'from_user_id' => LaravelExtend::getAuthUserId(),
                "sub_type" => Notification::SUB_TYPE_INFO,
                "msg" => "Lead $this->name is assigned to you"
            ];

            $notifyObj = new GeneralNotification($notification_data);

            $this->assignTo?->notify($notifyObj);
        }
    }

    public function changeStatus($status)
    {
        $cache_key = "status_type_active_list";

        $status_type_list = Type::getCache($cache_key);

        if (!$status_type_list)
        {
            $type_conditions = [
                'type' => Type::getType($this, "status_type")
            ];

            $status_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

            Type::addCache($cache_key, $status_type_list, 60 * 60 * 24);
        }

        $label = get_value_from_list($status_type_list, $this->status_type, $this->status_type);
        $label .= " To ";
        $label .= get_value_from_list($status_type_list, $status, $status);

        DashboardCounter::createOrUpdateCounter($this, "status_change", "", $label, 1, "", 10);

        $this->status_type = $status;
        $this->save();
    }

    public function changeNextFollowupDateTime($datetime)
    {
        $this->next_meeting_date_time = $datetime;
        $this->save();
    }

    public function canChangeHistory()
    {
        if ($this->assign_to_user_id)
        {
            $user_id_list = $this->assignTo->designation->getParentUserIdListBasedOnDesignationLevel();

            $auth_user_id = LaravelExtend::getAuthUserId();

            if ($auth_user_id == $this->assign_to_user_id || in_array($auth_user_id, $user_id_list))
            {

            }
            else
            {
                throw new UserException("You are not allowed to change lead history, only assignee or its parent designation user can change it.", UserException::ERROR_USER_ACTION_RESTRICT);
            }
        }
        else if (!is_admin_role_login())
        {
            throw new UserException("Lead is not assigned, so only Admin can change it.", UserException::ERROR_USER_ACTION_RESTRICT);
        }
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "state_id":
                $ret_array = [
                    $field => $this->state?->getDisplayName()
                ];
                break;
            case "district_id":
                $ret_array = [
                    $field => $this->district?->getDisplayName()
                ];
                break;
            case "city_id":
                $ret_array = [
                    $field => $this->city?->getDisplayName()
                ];
                break;
            case "assign_to_user_id":
                $ret_array = [
                    $field => $this->assignTo?->getDisplayName()
                ];
                break;
            case "dealer_id":
                $ret_array = [
                    $field => $this->dealer?->getDisplayName()
                ];
                break;
            case "campaign_id":
                $ret_array = [
                    $field => $this->campaign?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
