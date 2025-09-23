<?php

namespace App\Helpers;

<<<<<<< HEAD
=======
use App\Exceptions\UserException;
>>>>>>> base_v3
use Exception;

class ArrayHelper
{
<<<<<<< HEAD
    private Array $data; 
    public function __construct(Array $data)
=======
    private array $data;

    public const UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD = 1;

    public function __construct(array $data)
>>>>>>> base_v3
    {
        $this->data = $data;
    }

<<<<<<< HEAD
    protected function throw_exception($msg)
    {
        if (function_exists("throw_exception"))
        {
            throw_exception($msg);
        }
        else
        {
            $callBy = debug_backtrace()[1];

            $prefix = "ArrayHelper -> ";
            
            if (isset($callBy['function']))
            {
=======
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
>>>>>>> base_v3
                $call_fn_name = $callBy['function'];

                $prefix .= $call_fn_name . "() : ";
            }

            throw new Exception($prefix . $msg);
        }
    }

<<<<<<< HEAD
    public function getOnlyWhichHaveKeys(Array $keys, bool $recursive = false, bool $will_throw_exception_on_no_key_found = false)
    {
        $ret = [];

        foreach($keys as $k)
        {
            if (isset($this->data[$k]))
            {
                $ret[$k] = $this->data[$k];
            }
            else
            {
                if ($will_throw_exception_on_no_key_found)
                {
=======
    public function getOnlyWhichHaveKeys(array $keys, bool $recursive = false, bool $will_throw_exception_on_no_key_found = false)
    {
        $ret = [];

        foreach ($keys as $k) {
            if (isset($this->data[$k])) {
                $ret[$k] = $this->data[$k];
            } else {
                if ($will_throw_exception_on_no_key_found) {
>>>>>>> base_v3
                    throw new Exception("$k not found in Array");
                }
            }
        }

        return $ret;
    }
<<<<<<< HEAD
}
=======

    public function unsetKeys(array $keys)
    {
        foreach ($keys as $key) {
            if (isset($this->data[$key])) {
                unset($this->data[$key]);
            }
        }
    }

    public function unsetValues(array $data, array $find_values, bool $recursive = false, array $options = [])
    {
        if (empty($data))
        {
            $data = $this->data;
        }

        if (count($data) > 0)
        {
            $find_list = array_get_scaler_value_only($find_values);
            $list = array_get_scaler_value_only($data);            
            foreach ($find_list as $k => $value) {
                $key = array_search($value, $list);

                if ($key !== false) {
                    unset($data[$key]);
                }
            }
        }

        if ($recursive)
        {
            if (count($data) > 0)
            {
                foreach($data as $k => $v)
                {
                    if (is_array($v) && count($v) > 0)
                    {
                        $data[$k] = $this->unsetValues($v, $find_values, $recursive);

                        if (in_array(self::UNSET_VALUE_REMOVE_PARENT_IF_FOUND_CHILD, $options))
                        {
                            if (count($data[$k]) != count($v))
                            {
                                unset($data[$k]);
                            }
                        }
                    }
                }
            }
        }

        return $data;
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

    public function getValue($data = [], $find_value, bool $recursive = true)
    {
        if (!is_array($data) || empty($data))
        {
            $data = $this->data;
        }

        if (!is_scalar($find_value))
        {
            throw new UserException("arg 2nd: find_value value should be scaler");
        }

        $ret = [];

        foreach($data as $k => $v)
        {
            if (is_scalar($v))
            {
                if ($v == $find_value)
                {
                    $ret[$k] = $v;
                }
            }

            if (is_object($v))
            {
                $v = (array) $v;
            }

            if ($recursive && is_array($v))
            {                
                $ret[$k] = $this->getValue($v, $find_value, $recursive);
            }
        }

        return $ret;
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

    public function convertModelSearchConditionToList(array $data = [])
    {
        if (empty($data))
        {
            $data = $this->data;
        }

        $list = [];

        foreach($data as $arr)
        {
            $list[$arr[0]] = $arr[2] ?? $arr[1];
        }

        return $list;
    }

    public function extract (array $data = [], string $path)
    {
        if (empty($data))
        {
            $data = $this->data;
        }

        if (!$path)
        {
            return false;
        }
        
        $paths = explode(".", $path);
        
        $ret = array();
        
        foreach($data as $k => $arr)
        {
            if (count($paths) > 0)
            {                
                if ($k == $paths[0] || $paths[0] == "{n}")
                {
                    if (is_array($arr))
                    {
                        $new_paths = $paths;
                        unset($new_paths[0]);
                        if (!empty($new_paths))
                        {
                            $new_path = implode(".", $new_paths);
                            $ret = array_merge($ret, $this->extract($arr, $new_path) );
                        }
                        else
                        {
                            $ret[] = $arr;
                        }
                    }
                    else if (count($paths) == 1)
                    {
                        $ret[] = $arr;
                    }
                }
            }
        }
        
        return $ret;
    }
}
>>>>>>> base_v3
