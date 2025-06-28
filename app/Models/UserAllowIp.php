<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UserAllowIp extends BaseModel
{
    use HasFactory;

    protected static array $unique_fields = ["user_id", "ip"];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function getUniqueValidationRules(array $request_data)
    {
        $field_rules = [
            "ip" => [Rule::unique($this->connection . "." . $this->getTable(), "ip")],
        ];

        if ($this->id) {
            foreach ($field_rules as $field => $rules) {
                foreach ($rules as $rule) {
                    if ($rule instanceof Unique) {
                        $rule->ignore($this->id, $this->primaryKey);
                    }
                }
            }
        }

        return $field_rules;
    }
}
