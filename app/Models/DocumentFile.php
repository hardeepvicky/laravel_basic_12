<?php

namespace App\Models;

use App\Helpers\FileUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentFile extends BaseModel
{
    protected $appends = [
        'display_name',
    ];

    protected static array $file_fields = ["file"];

    public static array $child_model_class = [
        TaskFile::class => [
            "foreignKey" => "document_file_id",
            "preventDelete" => true
        ],
        RecurringTaskFile::class => [
            "foreignKey" => "document_file_id",
            "preventDelete" => true
        ],
        EmailTemplateFile::class => [
            "foreignKey" => "document_file_id",
            "preventDelete" => true
        ],
    ];

    public function documentCategory()
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->name . ' (' . $this->file_name . ')';
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
        ];

        return $field_rules;
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->font_awesome_icon = FileUtility::getFontAwesomeOnType($this->type);
    }

    public function beforeDelete() {
        parent::beforeDelete();

        $fileUtility = new FileUtility(self::getStorageDisk());

        if ($this->file)
        {
            $fileUtility->delete($this->file);
        }

        if ($this->file_original)
        {
            $fileUtility->delete($this->file_original);
        }
    }

    public function getTypeList()
    {
        $list = DB::table($this->getTable())->select('type')->distinct()->get()->pluck("type", "type")->toArray();

        foreach($list as $k => $v)
        {
            $list[$k] = ucwords($v);
        }

        return $list;
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch ($field) {
            case "document_category_id":
                $ret_array = [
                    $field => $this->documentCategory?->getDisplayName()
                ];
                break;
        }

        // dd($ret_array);

        return $ret_array;
    }
}
