<?php

namespace App\Helpers;

use App\Exceptions\UserException;
use Exception;

class ArrayHelper
{
    private array $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    protected function throw_exception($msg)
    {
        if (function_exists("throw_exception")) {
            throw_exception($msg);
        } else {
            $callBy = debug_backtrace()[1];

            $prefix = "ArrayHelper -> ";

            if (isset($callBy['function'])) {
                $call_fn_name = $callBy['function'];

                $prefix .= $call_fn_name . "() : ";
            }

            throw new Exception($prefix . $msg);
        }
    }

    public function getOnlyWhichHaveKeys(array $keys, bool $recursive = false, bool $will_throw_exception_on_no_key_found = false)
    {
        $ret = [];

        foreach ($keys as $k) {
            if (isset($this->data[$k])) {
                $ret[$k] = $this->data[$k];
            } else {
                if ($will_throw_exception_on_no_key_found) {
                    throw new Exception("$k not found in Array");
                }
            }
        }

        return $ret;
    }

    public function unsetKeys(array $keys)
    {
        foreach ($keys as $key) {
            if (isset($this->data[$key])) {
                unset($this->data[$key]);
            }
        }
    }

    public function unsetValues(array $values)
    {
        foreach ($values as $value) {
            if (is_scalar($value)) {
                $key = array_search($value, $this->data);

                if ($key) {
                    unset($this->data[$key]);
                }
            }
        }
    }

    public function onlyKeys(bool $recursive = false, array $arr = [])
    {
        if (empty($arr)) {
            $arr = $this->data;
        }

        foreach ($arr as $k => $v) {
            if (!is_numeric($k)) {
                if ($recursive && is_array($v) && count($v) > 0) {
                    $arr[$k] = $this->onlyKeys($recursive, $v);
                } else {
                    $arr[$k] = "";
                }
            }
        }

        return $arr;
    }

    public function getKeyIfValueExistInArray($data = [], $find_value, bool $recursive = false)
    {
        if (!is_array($data) || empty($data))
        {
            $data = $this->data;
        }

        if (!is_scalar($find_value))
        {
            throw new UserException("arg 2nd: find_value value should be scaler");
        }

        $keys = [];

        foreach($data as $k => $v)
        {
            if (is_array($v))
            {
                if (in_array($find_value, $v))
                {
                    $keys[$k] = $v;
                }

                if ($recursive)
                {
                    $keys[$k] = $this->getKeyIfValueExistInArray($v, $find_value, $recursive);
                }
            }
        }

        return $keys;
    }
}
