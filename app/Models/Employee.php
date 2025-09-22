<?php

namespace App\Models;

use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Employee extends BaseModel
{
    protected $appends = [
        'display_name'
    ];

    protected $casts = [
        'dob' => 'date:' . DateUtility::DATE_OUT_FORMAT,
        'doj' => 'date:' . DateUtility::DATE_OUT_FORMAT,
    ];

    protected static array $unique_fields = [];

    protected static array $list_fields = ["id", "name"];

    public static array $child_model_class = [        
    ];

    public function afterFindForCsv()
    {
        parent::afterFind();
    }
    
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function currentState()
    {
        return $this->belongsTo(State::class);
    }
    public function currentCity()
    {
        return $this->belongsTo(City::class);
    }
    public function permanentState()
    {
        return $this->belongsTo(State::class);
    }
    public function permanentCity()
    {
        return $this->belongsTo(City::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name;
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

    public function getRequiedIfRules(Request $request)
    {
        $rules = [];

        return $rules;
    }

    public function getProfileImage($defalut = "img/user.png")
    {
        if (isset($this->photo_url) && $this->photo_url) {
            return $this->photo_url;
        }

        if ($this->photo) {
            $fileUtility = new FileUtility($this->getStorageDisk());
            $url = $fileUtility->getUrl($this->photo);

            if ($url) {
                return $url;
            }

            return "/" . NOT_FOUND_IMAGE_PATH;
        }

        return $defalut;
    }
}
