<?php
namespace App\Traits;

use App\Exceptions\UserException;
use App\Helpers\ArrayHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use ReflectionMethod;

trait CommonMysqlModelTrait
{
    public function getNextId()
    {
        $table = $this->getTable();
        $statement = DB::select("SHOW TABLE STATUS LIKE '" . $table . "'");
        $nextId = $statement[0]->Auto_increment;

        return $nextId;
    }

    public function getDisplayFieldsToUser()
    {
        $attributes = $this->getAttributes();

        if (!$attributes) {
            $table_info = $this->getTableInfo($this->getTable());

            if (isset($table_info['columns']) || $table_info['columns']) {
                foreach ($table_info['columns'] as $field => $field_attr) {
                    $attributes[] = $field;
                }
            }
        }

        $arrayHelper = new ArrayHelper($attributes);
        $arrayHelper->unsetValues([
            "is_pre_defined",
            "can_edit",
            "cannot_edit_reason_array",
            "can_delete",
            "cannot_delete_reason_array",
        ]);

        return $arrayHelper->getData();
    }

    public function getFieldLabels()
    {
        $labels = [];

        $table_info = $this->getTableInfo($this->getTable());

        // d($table_info, true);

        $eception_list = [
            "code",
            "can_edit",
            "cannot_edit_reason_array",
            "can_delete",
            "cannot_delete_reason_array",
        ];

        foreach ($table_info['columns'] as $field => $field_attr) {

            if (!in_array($field, $eception_list)) {
                $text = str_replace(["_id", "is_"], "", $field);

                $labels[$field] = ucwords(str_function_name_to_human_text($text));
            }
        }

        // d($labels, true);

        return $labels;
    }
}