<?php

namespace App\Models;

class State extends BaseModel
{
    public array $child_model_class = [
        Employee::class => [
            "label" => "Employee",
            "foreignKey" => ["current_state_id", "permanent_state_id"],
            "preventDelete" => true
        ]
    ];

    public function city()
    {
        return $this->hasMany(City::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public static function createShortName($name)
    {
        if (!$name) {
            return "";
        }

        $str = str_convert_multi_space_to_single_space($name);

        $str = str_replace(["'"], "", $str);
        $str = str_replace("-", " ", $str);

        $words = explode(' ', $str); // Split the string by space

        $first_chars = [];

        $exception_words = ["and", "or", "by"];
        $exception_chars = ["\\", "/"];

        $retain_words = ["al", "ar", "la", "ash", "as", "su"];

        foreach ($words as $k => $word) {

            $char = "";

            $lower_word = strtolower($word);

            foreach ($retain_words as $retain_word) {
                if ($lower_word == $retain_word) {
                    $char = "-" . $retain_word . "-";
                }
            }

            if (in_array($lower_word, $exception_words)) {
                continue;
            }


            if (empty($char)) {
                $char = strtoupper($word[0]);
            }

            if (in_array($char, $exception_chars)) {
                continue;
            }

            $first_chars[$k] = $char;
        }

        $ret_str =  implode("", $first_chars);

        $ret_str = trim($ret_str, "-");

        $ret_str = trim($ret_str);

        return $ret_str;
    }
}
