<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailLog extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'api_response' => 'array'
    ];

    public static function getFileSavePath()
    {
        return "files/EmailLog/";
    }
}
