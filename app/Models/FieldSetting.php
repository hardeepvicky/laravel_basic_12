<?php

namespace App\Models;

class FieldSetting extends BaseMongoModel
{
    protected static array $unique_fields = ["type"];

    public static function getDynamicFields($model_class)
    {
        $model_class = str_class_name_without_namespace($model_class);

        $ref_model_class = "App\\Models\\" . $model_class;

        $ref_model = $ref_model_class::query()->first();

        if (!$ref_model) {
            $ref_model = new $ref_model_class();
        }

        $fields = $ref_model->getFieldLabels();

        $record = self::where("type", $model_class)->first();

        if (isset($record->field_labels) && is_array($record->field_labels) && count($record->field_labels) > 0) {
            foreach ($record->field_labels as $field => $custom_label) {
                if (is_string($custom_label) && strlen(trim($custom_label)) > 0) {
                    $fields[$field] = trim($custom_label);
                }
            }
        }

        return $fields;
    }
}
