<?php

namespace App\Models;

class AppMenuRelatedLink extends BaseModel
{
    protected static Array $unique_fields = ["app_menu_id", "route_name"];

    protected $casts = [
        "route_name_array" => "array"
    ];

    public function appMenu()  
    {
        return $this->belongsTo(AppMenu::class);
    }

    public function getUniqueValidationRules($data)
    {
        return [];
    }
}
