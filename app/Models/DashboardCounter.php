<?php

namespace App\Models;
use Illuminate\Support\Str;

class DashboardCounter extends BaseMongoModel
{
    public static function getStoragePath()
    {
        return "logs/DashboardCounter/";
    }

    public static function createOrUpdateCounter(BaseModelInterface $model, string $prefix, string $name, string $label, float $count, string $sub_title = "", int $display_order = 0, string $css_class = "primary")
    {
        if (!$name)
        {
            $name = Str::snake($label);
        }

        $save = [
            "model" => str_class_name_without_namespace($model),
            "name" => $prefix . "-" . $name,
            "label" => $label,
            "sub_title" => $sub_title,
            "count" => round($count, 2),
            "up" => 0,
            "down" => 0,
            "display_order" => $display_order,
            "css_class" => $css_class,
        ];
        

        $model = self::where("model", $save['model'])->where("name", $save['name'])->first();

        if ($model)
        {
            unset($save['model']);
            unset($save['name']);
            unset($save['count']);
            unset($save['up']);
            unset($save['down']);

            $new_count = $model->count + $count;

            $old_count = $model->count;

            if ($new_count > $model->count)
            {
                $diff = round($new_count - $model->count, 3);
                $save['up'] = round($diff * 100 / $model->count);
            }
            else if ($new_count < $model->count)
            {
                $diff = round($model->count - $new_count, 3);
                $save['up'] = round($diff * 100 / $model->count);
            }

            $model->fill($save);

            $model->count = $new_count;

            $model->save();
        }
        else
        {
            $model = static::create($save);
        }

        return $model;
    }
}
