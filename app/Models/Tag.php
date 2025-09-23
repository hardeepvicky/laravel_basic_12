<?php

namespace App\Models;

use App\Exceptions\UserException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class Tag extends BaseModel
{
    public static array $child_model_class = [
       
    ];

    protected static array $unique_fields = ["main_model", "main_table_field", "main_id", "name"];

    protected $appends = [
        'display_name',
    ];

    public function beforeSave()
    {
        if(!$this->search_term)
        {
            $str = str_class_name_to_human_text($this->main_model);
            $this->search_term = $str . "." . $this->name;
        }

        $required_keys = [
            "main_model",
            "main_table",
            "main_id",
            "name",
            "search_term",
        ];

        UserException::checkKeysExistOrThrowException($this->getAttributes(), $required_keys);

        parent::beforeSave();
    }

    protected function getDisplayNameAttribute()
    {
        return $this->getDisplayName();
    }

    public function getDisplayName()
    {
        return $this->search_term;
    }

    public function getUniqueValidationRules(Request $request)
    {
        $field_rules = [
            "search_term" => [Rule::unique($this->connection . "." . $this->getTable(), "name")],
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

    public static function createTag(array $save_data, Model $main_model, Model | null $ref_model = null)
    {
        $model_class_name = str_class_name_without_namespace(get_class($main_model));

        if (!$model_class_name)
        {
            throw new UserException("could not found model_class_name");
        }

        $main_table_name = $main_model->getTable();

        $save_data = array_merge($save_data, [
            "main_model" => $model_class_name,
            "main_table" => $main_table_name,
        ]);

        if (!isset($save_data['main_id']))
        {
            $save_data['main_id'] = $main_model->id;
        }

        if (!is_null($ref_model))
        {
            $ref_model_class_name = str_class_name_without_namespace(get_class($ref_model));

            if (!$ref_model_class_name)
            {
                throw new UserException("could not found ref_model_class_name");
            }

            $ref_table_name = $ref_model->getTable();

            $save_data = array_merge($save_data, [
                "main_model" => $model_class_name,
                "main_table" => $main_table_name,
                "main_id" => $ref_model->id,
                "foreign_model" => $model_class_name,
                "foreign_table" => $ref_table_name,
                "foreign_id" => $ref_model->id
            ]);
        }

        $model = self::insertOrUpdate($save_data, $is_insert, $is_update);

        return compact("model", "is_insert","is_update");
    }
}
