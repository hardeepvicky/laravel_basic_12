<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Role extends BaseModel
{
    use HasFactory;

<<<<<<< HEAD
    /**
     * name of table fields which uniquly identify the record
     */
    protected static Array $unique_fields = ["name"];
=======
    const TYPE_SYSTEM_ADMIN = "system_admin";
    const TYPE_DEVELOPER = "developer";

    /**
     * name of table fields which uniquly identify the record
     */
    protected static array $unique_fields = ["name"];

    protected static array $list_fields = ["id", "name", "code"];
>>>>>>> base_v3

    protected $appends = [
        'display_name',
    ];

    /**
     * set extra relationship array to overcome problem of accidential delete
     * this variable used in Controller.php -> delete()
     */
<<<<<<< HEAD
    public Array $child_model_class = [
=======
    public static array $child_model_class = [
>>>>>>> base_v3
        UserRole::class => [
            "foreignKey" => "role_id",
            "preventDelete" => true,
            "label" => "User's Role"
        ],
        RoleRouteName::class => [
            "foreignKey" => "role_id",
            "preventDelete" => false,
            "label" => "Routes"
        ],
    ];

<<<<<<< HEAD
    const TYPE_SYSTEM_ADMIN = "system_admin";    
=======

>>>>>>> base_v3

    public function userRole()
    {
        return $this->hasMany(UserRole::class, 'role_id');
    }

    public function routeNames()
    {
        return $this->belongsToMany(RouteName::class, RoleRouteName::class);
    }

<<<<<<< HEAD
    public function scopeWithAll($query) 
=======
    public function scopeWithAll($query)
>>>>>>> base_v3
    {
        $query->with('userRole');
    }

<<<<<<< HEAD
    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name,
        );
=======
    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
>>>>>>> base_v3
    }
}
