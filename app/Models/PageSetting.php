<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSetting extends BaseMongoModel
{
    protected static array $unique_fields = ["type"];

    public static function getDynamicFields($type)
    {
        $type = str_class_name_without_namespace($type);

        $cache_key = "-getDynamicFields-" . $type;

        $fields = self::getCache($cache_key);

        if ($fields) {
            return $fields;
        }


        $ref_model_class = "App\\Models\\" . $type;

        $ref_model = $ref_model_class::query()->first();

        if (!$ref_model) {
            $ref_model = new $ref_model_class();
        }

        $fields = $ref_model->getFieldLabels();

        $record = self::where("type", $type)->first();

        if (isset($record->field_labels) && is_array($record->field_labels) && count($record->field_labels) > 0) {
            foreach ($record->field_labels as $field => $custom_label) {
                if (is_string($custom_label) && strlen(trim($custom_label)) > 0) {
                    $fields[$field] = trim($custom_label);
                }
            }
        }

        self::addCache($cache_key, $fields);

        return $fields;
    }
}
