<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAllowIp extends BaseModel
{
    use HasFactory;

    protected static array $unique_fields = ["user_id", "ip"];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
