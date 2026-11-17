<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleRouteName extends BaseModel
{
    use HasFactory;

    protected static Array $unique_fields = ["role_id", "route_name_id"];

    public function role()  
    {
        return $this->belongsTo(Role::class, "role_id");
    }

    public function routeName()
    {
        return $this->belongsTo(RouteName::class, "route_name_id");
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch ($field) {
            case "role_id":
                $ret_array = [
                    $field => $this->role?->getDisplayName()
                ];
                break;
            case "route_name_id":
                $ret_array = [
                    $field => $this->routeName?->name
                ];
                break;
        }

        // dd($ret_array);

        return $ret_array;
    }
}
