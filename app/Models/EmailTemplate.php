<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class EmailTemplate extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected $casts = [
        'placeholders' => 'array',
    ];

    public static array $child_model_class = [
       
    ];

    protected static array $unique_fields = ["name"];

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    public function documentFile()
    {
        return $this->belongsToMany(DocumentFile::class, EmailTemplateFile::class);
    }

    public function emailTemplateFile()
    {
        return $this->hasMany(EmailTemplateFile::class, 'email_template_id');
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [];

        if ($request->has("name"))
        {
            $field_rules['name'] = [Rule::unique($this->connection . "." . $this->getTable(), "name")];
        }

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

        $labels['emailTemplateFile'] = "Files";

        return $labels;
    }

    public function getBodyAndSubject(array $placeholder_data, User $user) : array
    {
        $subject = $this->subject;
        $body = $this->body;

        $placeholder_values = $this->resolvePlaceholders($placeholder_data, $user);

        foreach($placeholder_values as $placeholder => $value)
        {
            if (is_scalar($value))
            {
                $subject = str_replace("@". $placeholder, $value, $subject);
                $body = str_replace("@". $placeholder, $value, $body);
            }
        }

        return compact("subject", "body");
    }

    public function resolvePlaceholders(array $placeholder_data, User $user) : array
    {
        $default_placeholder_data = $this->getPlaceholderCommonData();

        $placeholder_data = array_merge($default_placeholder_data, $placeholder_data);

        $placeholder_data['unsubscribe_link'] = route("unsubscribe_email", ["uid" => $user->uid]);

        $placeholder_data['user.name'] = $user->full_name;
        $placeholder_data['user.email'] = $user->email;
        $placeholder_data['user.mobile'] = $user->mobile ?? "";
        $placeholder_data['user.whatsapp_mobile'] = $user->whatsapp_mobile ?? "";
        $placeholder_data['user.otp'] = $user->otp ?? "";
        $placeholder_data['user.country'] = $user->country->getDisplayName();
        $placeholder_data['user.country.phone_code'] = $user->country->phonecode ?? "";
        $placeholder_data['user.country.short_name'] = $user->country->short_name ?? "";
        $placeholder_data['user.country.iso'] = $user->country->iso3 ?? "";

        return $placeholder_data;
    }

    public static function getPlaceholderCommonData() : array
    {
        $placeholders = [
            "company.name" => "",
            "company.email" => "",
            "company.tag_line" => "",
            "company.address" => "",
            "company.gst_no" => "",
            "company.logo" => "",
        ];

        $setting_list = Setting::getValueOfList(["company_name", "company_tag_line", "company_email", "company_address", "company_gst_no"]);

        $placeholders['company.name'] = $setting_list['company_name'];
        $placeholders['company.tag_line'] = $setting_list['company_tag_line'];
        $placeholders['company.email'] = $setting_list['company_email'];
        $placeholders['company.address'] = $setting_list['company_address'];
        $placeholders['company.gst_no'] = $setting_list['company_gst_no'];

        $placeholders['company.logo'] = url("/assets/images/logo/logo.png");

        return $placeholders;
    }
}


