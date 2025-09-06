<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ExpParty extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $unique_fields = ["type", "name"];

    protected static array $list_fields = ["id", "name", "type"];

    const TYPE_OEM = "OEM";
    const TYPE_VENDOR = "VENDOR";
    const TYPE_SUPPLIER = "SUPPLIER";
    const TYPE_CONTRACTOR = "CONTRACTOR";
    const TYPE_CARRIER = "CARRIER";

    public static array $child_model_class = [
        // TaskParty::class => [
        //     "foreignKey" => ["party_id"],
        //     "preventDelete" => true
        // ],        
    ];

    protected static array $fields_containg_tags = ["description"];

    public string $field_for_create_tag = "display_name";

    public function afterSave()
    {
        ExportContact::createContact("party", $this->id);

        return parent::afterSave();
    }

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
        return $this->belongsToMany(DocumentFile::class, PartyFile::class);
    }

    public function partyFile()
    {
        return $this->hasMany(PartyFile::class, 'exp_party_id');
    }

    public function terms()
    {
        return $this->hasMany(PartyTerm::class, 'exp_party_id');
    }

    public function address()
    {
        return $this->hasMany(PartyAddress::class, 'exp_party_id');
    }

    public function state()
    {
        return $this->belongsTo(ExpState::class);
    }

    public function country()
    {
        return $this->belongsTo(ExpCountry::class);
    }

    public function city()
    {
        return $this->belongsTo(ExpCity::class);
    }

    public function history()
    {
        return $this->hasMany(ExpPartyHistory::class, 'exp_party_id');
    }

    public function assignTo()
    {
        return $this->belongsTo(User::class, "assign_to_user_id");
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
            self::TYPE_OEM => "OEM",
            self::TYPE_VENDOR => "Vendor",
            self::TYPE_SUPPLIER => "Supplier",
            self::TYPE_CONTRACTOR => "Contractor",
            self::TYPE_CARRIER => "Carrier",
        ];

        return $list;
    }

    public function getFieldLabels()
    {
        $labels = parent::getFieldLabels();
        $labels['terms'] = "Terms and Conditions";
        $labels['partyFile'] = "Files";

        return $labels;
    }

    
    public function changeStatus($status)
    {
        $cache_key = "status_type_active_list";

        $status_type_list = Type::getCache($cache_key);

        if (!$status_type_list)
        {
            $type_conditions = [
                'type' => Type::getType(new Lead(), "status_type")
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
        $this->last_followup_date_time = $this->next_followup_date_time;
        $this->next_followup_date_time = $datetime;
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

    public function changeAssignTo(array $data)
    {
        if ($data['assign_to_user_id'])
        {
            $this->assign_to_user_id = $data['assign_to_user_id'];
            $this->save();

            $data['party_id'] = $this->id;
            PartyHistory::create($data);

            $notification_data = [
                'from_user_id' => LaravelExtend::getAuthUserId(),
                "sub_type" => Notification::SUB_TYPE_INFO,
                "msg" => "Dealer $this->name is assigned to you"
            ];

            $notifyObj = new GeneralNotification($notification_data);

            $this->assignTo?->notify($notifyObj);
        }
    }
}
