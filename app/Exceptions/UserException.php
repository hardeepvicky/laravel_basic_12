<?php

namespace App\Exceptions;

use Exception;

class UserException extends Exception
{
    public const ERROR_GENERAL = 512;
    public const ERROR_LARAVEL_FAIL = 513;
    public const ERROR_PHP_FAIL = 514;
    public const ERROR_FROM_VALIDATION = 515;
    public const ERROR_USER_ACTION_RESTRICT = 516;
    public const ERROR_USER_DATA_WRONG = 517;

    public $data;
    public function __construct(String $message, int $code = 512, $data = [])
    {
        parent::__construct($message, $code);
        $this->setData($data);
    }

    public function setData($data)
    {
        $data['error_title'] = "Unknown Error";
        $data['file_name'] = $this->getFile();
        $data['file_line_no'] = $this->getLine();

        if (!isset($data['html']))
        {
            $data['html'] = "";
        }

        switch ($this->code) {
            case self::ERROR_GENERAL:
                $data['error_title'] = "Error bcoz of Developer's mistake";
                break;

            case self::ERROR_LARAVEL_FAIL:                
                $data['error_title'] = "Error bcoz of Laravel internal failure";
                break;

            case self::ERROR_PHP_FAIL:
                $data['error_title'] = "Error bcoz of PHP internal failure";
                break;

            case self::ERROR_USER_ACTION_RESTRICT:
                $data['error_title'] = "Restriction";
                break;

            case self::ERROR_USER_DATA_WRONG:
                $data['error_title'] = "Error due to wrong or missing data in database";
                break;
        }

        $this->data = $data;
    }

    public static function checkKeyExistOrThrowException(array $list, $key, array $options = [])
    {
        if (!is_scalar($key))
        {
            throw new UserException("arg 2 key should be scaler");
        }

        if (!isset($list[$key]))
        {
            $valid_keys = str_short_with_dot(implode(", ", array_keys($list)));

            throw new UserException("Invalid key : $key in list, Valid Keys are : " . $valid_keys);
        }
    }

    public static function checkValueExistOrThrowException(array $list, $value, array $options = [])
    {
        if (!is_scalar($value))
        {
            throw new UserException("arg 2 value should be scaler");
        }

        if (!in_array($value, $list))
        {
            $valid = str_short_with_dot(implode(", ", $list));

            throw new UserException("Invalid value : $value in list, Valid values are : " . $valid);
        }
    }

    public static function checkPossibleValues($find_value, array $possible_values, array $options = [])
    {
        if (!is_scalar($find_value))
        {
            throw new UserException("arg 1 find_value should be scaler");
        }

        if (in_array($find_value, $possible_values))
        {
            return $find_value;
        }
        else
        {
            $converted_find_value = $find_value;
            if (isset($options['before_check_convert_string']) && is_array($options['before_check_convert_string']))
            {
                foreach($options['before_check_convert_string'] as $str_function_name)
                {
                    if (!function_exists($str_function_name))
                    {
                        throw new UserException("wrong function name : str_function_name");
                    }
                    
                    $converted_find_value = $str_function_name($converted_find_value);

                    foreach($possible_values as $k => $v)
                    {
                        $possible_values[$k] = $str_function_name($v);
                    }
                }
            }

            if (in_array($converted_find_value, $possible_values))
            {
                return $converted_find_value;
            }
            
            $possible_value_str = str_short_with_dot(implode(", ", array_keys($possible_values)));

            throw new UserException("Invalid value : $find_value, Possible values are : " . $possible_value_str);
        }
    }
}
