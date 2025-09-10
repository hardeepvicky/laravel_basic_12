<?php

namespace App\Models;

class NotificationSetting extends BaseMongoModel
{
    public const LABELS = [
        "on_dispatch_order_from_sales_lead" => "On Dispatch Order From Sales -> Lead",
        "on_ship_order_from_export_lead" => "On Ship Order From Exports -> Lead",
    ];

    const TYPE_ROLE = "ROLE";
    const TYPE_DEPARTMENT = "DEPARTMENT";

    const SUB_TYPE_DESIGNATION = "DESIGNATION";
    const SUB_TYPE_USER = "USER";

    /**
     * name of table fields which uniquly identify the record
     */
    protected static array $unique_fields = ["name"];

    /**
     * set extra relationship array to overcome problem of accidential delete
     * this variable used in Controller.php -> delete()
     */
    public static function getUserIdList($name)
    {
        $record = self::where("name", $name)->first();

        if (is_null($record)) {
            $label = self::LABELS[$name] ?? $name;
            abort(ACTION_NOT_PROCEED, "Notification Setting : $label is not set yet");
        }

        if ($record)
        {
            $role_id_list = $dept_id_list = $desg_id_list = $user_id_list = [];
            switch($record->type)
            {
                case self::TYPE_ROLE:
                    $role_id_list = $record->role_id_list;
                    break;
                case self::TYPE_DEPARTMENT:
                    $dept_id_list = $record->dept_id_list;
                    break;
            }

            switch($record->sub_type)
            {
                case self::SUB_TYPE_DESIGNATION:
                    $desg_id_list = $record->desg_id_list;
                    break;
                case self::SUB_TYPE_USER:
                    $user_id_list = $record->user_id_list;
                    break;
            }

            if ($user_id_list)
            {
                return $user_id_list;
            }

            $builder = User::query();

            if ($role_id_list)
            {
                $builder->whereHas("role", function ($q) use($role_id_list) {
                    $q->whereIn("id", $role_id_list);
                });
            }

            if ($dept_id_list)
            {
                $builder->whereIn("dept_id", $dept_id_list);
            }

            if ($desg_id_list)
            {
                $builder->whereIn("dept_id", $desg_id_list);
            }

            $user_id_list = $builder->pluck("id")->toArray();
        }

        return [];
    }

    public static function getTypeList(){
        return [
            self::TYPE_ROLE => "To Roles",
            self::TYPE_DEPARTMENT => "To Departments",
        ];
    } 

    public static function getSubTypeList(){
        return [
            self::SUB_TYPE_DESIGNATION => "To Designation",
            self::SUB_TYPE_USER => "To Users",
        ];
    } 
}
