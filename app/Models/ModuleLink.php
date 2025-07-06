<?php

namespace App\Models;

use App\Helpers\LaravelExtend;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ModuleLink extends BaseModel
{
    public static function saveLinksFromStringPattern(Model $model, string $content)
    {
        $save_count = 0;

        $base_save_data = [
            "main_id" => $model->id,
            "main_table" => $model->getTable(),
        ];

        $base_save_data['main_model'] = str_class_name_without_namespace(get_class($model));
        
        self::where("main_model", $base_save_data['main_model'])
                ->where("main_id", $base_save_data['main_id'])
                ->delete();
                
        $pattern = '/#([a-zA-Z]+)\.([A-Za-z]+):([0-9]+)/i';

        preg_match_all($pattern, $content, $main_matches);

        if (empty($main_matches) || !isset($main_matches[0]) || empty($main_matches[0])) {
            return $save_count;
        }

        foreach ($main_matches[0] as $main_match) {
            preg_match_all('/#[a-zA-Z]+/i', $main_match, $matches);

            if (isset($matches[0])) {
                $type = str_replace("#", "", $matches[0][0]);

                $ref_model_class = "App\\Models\\" . $type;

                $ref_model = new $ref_model_class();

                preg_match_all('/\.[a-zA-Z]+/i', $main_match, $matches);

                if (isset($matches[0])) {
                    $field = str_replace(".", "", $matches[0][0]);

                    preg_match_all('/:[0-9]+/', $main_match, $matches);

                    if (isset($matches[0])) {
                        $id = str_replace(":", "", $matches[0][0]);

                        if ($id) {

                            $save_data = array_merge($base_save_data, [
                                "foreign_table" => $ref_model->getTable(),
                                "foreign_model" => $type,
                                "foreign_id" => $id,
                            ]);

                            self::create($save_data);

                            $save_count++;
                        }
                    }
                }
            }
        }

        return $save_count;
    }

    public static function parseStringToMakeLinks(Model $model, string $content)
    {
        // $linked_smodules = ModuleLink::where("main_model", $model_name)->where("main_id", $model->id)->get();

        // if (!$linked_modules)
        // {
        //     return $content;
        // }

        // dd($linked_modules);

        $pattern = '/#([a-zA-Z]+)\.([A-Za-z]+):([0-9]+)/i';

        preg_match_all($pattern, $content, $main_matches);

        if (empty($main_matches) || !isset($main_matches[0]) || empty($main_matches[0])) {
            return;
        }

        foreach ($main_matches[0] as $main_match) {
            preg_match_all('/#[a-zA-Z]+/i', $main_match, $matches);

            if (isset($matches[0])) {
                $type = str_replace("#", "", $matches[0][0]);

                preg_match_all('/\.[a-zA-Z]+/i', $main_match, $matches);

                if (isset($matches[0])) {
                    $field = str_replace(".", "", $matches[0][0]);

                    preg_match_all('/:[0-9]+/', $main_match, $matches);

                    if (isset($matches[0])) {
                        $id = str_replace(":", "", $matches[0][0]);

                        if ($id) {
                            // $tag_link = ModuleLink::where("main_model", $model_name)
                            //         ->where("main_id", $model->id)
                            //         ->with("tag")
                            //         ->first();


                            $url = LaravelExtend::getViewLinkForModel($type, $id);

                            if ($url)
                            {
                                $search = "#" . $type . ".id:" . $id;

                                $anchor = '<a target="_blank" href="' . $url . '">Open Link</a>';

                                $content = str_replace($search, $anchor, $content);
                            }
                           
                        }
                    }
                }
            }
        }


        return $content;
    }
}
