<?php

use App\Exceptions\UserException;
use App\Helpers\ConsoleOutput;
use App\Helpers\DateUtility;
use App\Helpers\Util;
use App\Models\Role;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

function throw_exception($msg)
{
    $callBy = debug_backtrace()[1];

    $prefix = "";

    if (isset($callBy['class'])) {
        $call_class_name = $callBy['class'];

        $prefix .= $call_class_name . "->";
    }

    if (isset($callBy['function'])) {
        $call_fn_name = $callBy['function'];

        $prefix .= $call_fn_name . "() : ";
    }

    throw new Exception($prefix . $msg);
}


function d($arg, $will_exit = false)
{
    $callBy = debug_backtrace()[0];

    if (!env("APP_DEBUG")) {
        return;
    }

    if (App::runningInConsole()) {
        $console = new ConsoleOutput();

        $console->addLogMessage("File : " . $callBy['file']);
        $console->addLogMessage("Line : " . $callBy['line']);            
        $console->addLogMessage("Data Type : " . gettype($arg));
        $console->writeLogToConsole();

        dump($arg);

    } else {

        echo "<pre>";
        echo "<b>" . $callBy['file'] . "</b> At Line : " . $callBy['line'];
        echo "<br/>";

        if (is_string($arg)) {
            echo htmlspecialchars($arg);
        } else if (is_bool($arg)) {
            echo $arg ? "True" : "False";
        } else if (is_null($arg)) {
            echo "NULL";
        } else {
            print_r($arg);
        }

        echo "</pre>";        
    }

    if ($will_exit) {
        exit;
    }
}


function print_var_name($var)
{
    foreach ($GLOBALS as $var_name => $value) {
        if ($value === $var) {
            return $var_name;
        }
    }

    return false;
}

function print_for_debug($arg, $label = "", bool $show_data_type = false, $depth = 0)
{
    echo '<ul class="array_print indent-' . $depth . '" >';

    $type = gettype($arg);

    echo '<li>';

    if ($show_data_type)
    {
        echo ' (' . $type . ") : ";
    }

    if ($label)
    {
        echo " " . $label . " => ";
    }

    if ($type == "object")
    {
        $arg = (array) $arg;
    }

    if($type == "array") { 
        if (empty($arg))               
        {
            echo "[]";
        }
        else
        {
            foreach ($arg as $key => $value) {
                print_for_debug($value, $key, $show_data_type, $depth + 1);
            }
        }
    }
    else
    {
        if (is_bool($arg)) {
            echo  $arg ? "True" : "False";
        } else if (is_null($arg)) {
            echo "NULL";
        }
        else
        {
            print_r(htmlspecialchars($arg));
        }         
    }
    
    echo '</li>';
    echo '</ul>';
}

function array_print($array, int $max_string_len = 45, int $indent = 0)
{
    if (is_array($array)) {
        $array = array_short_string_with_dot($array, $max_string_len, false);


        echo '<ul class="array_print indent-' . $indent . '" >';
        foreach ($array as $key => $value) {
            echo '<li>';
            if (is_array($value)) {
                echo '<strong>' . htmlspecialchars($key) . ':</strong>';
                array_print($value, $max_string_len, $indent + 1); // Recurse for nested arrays
            } else {
                echo '<strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars(print_r($value, true));
            }
            echo '</li>';
        }

        echo '</ul>';
    }
}


function obj_print($arg, $max_string_len = 45, $indent = 0)
{
    if (is_object($arg)) {
        array_print($arg, $max_string_len, $indent);
    }
}


function get_sub_domain()
{
    $host = $_SERVER['APP_URL'] ?? "";
    $hostParts = explode('.', $host); // Remove the last two parts (domain and TLD) 
    $subdomain = implode('.', array_slice($hostParts, 0, -2));
    return $subdomain;
}


function get_query_params_of_current_url_in_array()
{
    $urlComponents = parse_url($_SERVER['REQUEST_URI']);

    $query_params = [];
    if (isset($urlComponents['query']) && $urlComponents['query']) {
        $query_list = explode("&", $urlComponents['query']);

        foreach ($query_list as $str) {

            $arr3 = explode("=", $str);

            if ($arr3[0]) {
                $query_params[$arr3[0]] = $arr3[1];
            }
        }
    }

    Util::applyAllforKey(Util::applyAll($query_params, ["trim"]), ["trim"]);

    return $query_params;
}

function get_url($path, $extra_params = [])
{
    $query_params = get_query_params_of_current_url_in_array();

    $query_params = array_merge($query_params, $extra_params);

    $query_list = [];

    foreach ($query_params as $k => $v) {
        $query_list[] = "$k=$v";
    }

    return $path . "?" . implode("&", $query_list);
}

function get_current_path_url($extra_params = [])
{
    $urlComponents = parse_url($_SERVER['REQUEST_URI']);

    return get_url($urlComponents['path'], $extra_params);
}


function sortable_url($sort_by)
{
    $query_params = get_query_params_of_current_url_in_array();
    unset($query_params['is_sort_clear']);

    $query_params['sort_by'] = $sort_by;

    if (isset($query_params['sort_dir'])) {
        $query_params['sort_dir'] = strtoupper($query_params['sort_dir']);

        if ($query_params['sort_dir'] == 'ASC') {
            $query_params['sort_dir'] = 'DESC';
        } else if ($query_params['sort_dir'] == 'DESC') {
            $query_params['sort_dir'] = 'ASC';
        }
    } else {
        $query_params['sort_dir'] = 'ASC';
    }

    $urlComponents = parse_url($_SERVER['REQUEST_URI']);

    foreach ($query_params as $k => $v) {
        $query_list[] = "$k=$v";
    }

    return $urlComponents['path'] . "?" . implode("&", $query_list);
}

// dd(sortable_url("id"));

function sortable_anchor(String $sort_by, String $title, array $attrs = [])
{
    $url = sortable_url($sort_by);

    $html = '<a class="sortable" href="' . $url . '"';

    $atrr_list = [];
    foreach ($attrs as $k => $v) {
        if (is_numeric($k)) {
            $k = $v;
        }

        $atrr_list[] = $v . '="' . $v . '"';
    }

    $html .= " " . implode(" ", $atrr_list);
    $html = trim($html);
    $html .= '>';

    $content_html = $title;

    if (isset($_GET['sort_dir']) && isset($_GET['sort_by']) && $_GET['sort_by'] == $sort_by) {
        if (strtoupper(trim($_GET['sort_dir'])) == 'DESC') {
            $content_html .= ' ' . '<i class="fas fa-arrow-up"></i>';
        } else {
            $content_html .= ' ' . '<i class="fas fa-arrow-down"></i>';
        }
    }

    $html .= $content_html;

    $html .= '</a>';

    return $html;
}

function if_date_time($datetime)
{
    if ($datetime) {
        return DateUtility::getDate($datetime, DateUtility::DATETIME_OUT_FORMAT);
    }

    return "";
}

function if_date($datetime)
{
    if ($datetime) {
        return DateUtility::getDate($datetime, DateUtility::DATE_OUT_FORMAT);
    }

    return "";
}

function download_start($file, $content_type, $delete_after_download = false)
{
    header('Content-Description: File Transfer');
    header("Content-Type: $content_type");
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    if ($delete_after_download) {
        unlink($file);
    }
    exit;
}

function curl_get_request($url, $headers = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);
    curl_close($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    return $res;
}

function curl_post_request($url, $params, $headers = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    curl_close($ch);

    return $res;
}

function str_class_name_without_namespace($class_name)
{
    if (is_object($class_name))
    {
        $class_name = get_class($class_name);
    }

    if (is_string($class_name) && strpos($class_name, "\\") >= 0) {
        $arr = explode("\\", $class_name);

        if ($arr) {
            $class_name = end($arr);

            return $class_name;
        }
    }

    return null;
}

function str_space_before_every_capital_letter($str)
{
    return trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $str));
}

function str_class_name_to_human_text($class_name)
{
    $str = str_class_name_without_namespace($class_name);

    $str = str_space_before_every_capital_letter($str);

    return $str;
}

function str_function_name_to_human_text($class_name)
{
    $str = str_class_name_without_namespace($class_name);

    $str = str_space_before_every_capital_letter($str);

    $str = str_replace("_", " ", $str);

    return $str;
}

function array_check_key_and_throw_error($arr, $keys, $msg = "")
{
    if (!$msg) {
        $msg = "Key : {key} not found in array";
    }

    foreach ($keys as $key) {
        if (!array_key_exists($key, $arr)) {
            $msg = str_replace("{key}", $key, $msg);
            throw new Exception($msg);
        }
    }
}

function array_check_value_and_throw_error($arr, $keys, $msg = "")
{
    if (!$msg) {
        $msg = "Key : {key} is empty in array ";
    }

    foreach ($keys as $key) {
        if (!isset($arr[$key]) || strlen($arr[$key]) == 0 || is_null($arr[$key])) {
            $msg = str_replace("{key}", $key, $msg);
            throw new Exception($msg);
        }
    }
}

function laravel_constant($name)
{
    $key = "constant." . $name;

    if (!config()->has($key)) {
        throw_exception("Constant : $name is not exist in config->constant.php file");
    }

    return config($key);
}


function amount_with_dr_cr($amount)
{
    if ($amount > 0) {
        return "Dr. " . $amount;
    } else if ($amount < 0) {
        return "Cr. " . $amount;
    }

    return $amount;
}

function str_check_char_array_exist(string $str, $to_check)
{
    if (is_array($to_check)) {
        foreach ($to_check as $needle) {
            $pos = strpos($str, $needle);

            if (is_int($pos) && $pos >= 0) {
                return true;
            }
        }
    } else if (is_string($to_check)) {
        $pos = strpos($str, $to_check);

        if (is_int($pos) && $pos >= 0) {
            return true;
        }
    }

    return false;
}

function array_make_all_values_zero_if_null($arr)
{
    foreach ($arr as $k => $v) {
        if (is_null($v)) {
            $arr[$k] = 0;
        }
    }

    return $arr;
}

function array_extract_only_of_keys(array $arr, array $keys, bool $throw_exception_on_key_not_found = false, $msg = "")
{
    if (!$msg) {
        $msg = "Key : {key} is not found in array ";
    }

    $ret_arr = [];

    foreach ($keys as $key) {
        if (isset($arr[$key])) {
            $ret_arr[$key] = $arr[$key];
        } else {
            if ($throw_exception_on_key_not_found) {
                $msg = str_replace("{key}", $key, $msg);
                throw new Exception($msg);
            }
        }
    }

    return $ret_arr;
}

function array_make_empty_string_if_keys_not_found(array $arr, array $keys)
{
    foreach ($keys as $key) {
        if (!isset($arr[$key])) {
            $arr[$key] = "";
        }
    }

    return $arr;
}


function get_cache_prefix()
{
    return "";
}

function str_protected($str)
{
    if (!is_string($str)) {
        return "";
    }

    $len = strlen($str);

    $first_part = substr($str, 0, 3);

    $email_pos = strpos($str, "@");

    if ($email_pos !== false) {
        $last_part = substr($str, $email_pos);
    } else {
        $last_part = substr($str, -3);
    }

    return $first_part . "*****" . $last_part;
}

function get_reflection_method_human_list()
{
    $class = new ReflectionClass(ReflectionMethod::class);

    $constants = $class->getConstants();

    $list = [];

    foreach ($constants as $constant => $code) {
        $str = str_replace("IS_", "", $constant);

        $str = ucfirst(strtolower($str));

        $list[$code] = $str;
    }

    return $list;
}


function get_reflection_property_human_list()
{
    $class = new ReflectionClass(ReflectionProperty::class);

    $constants = $class->getConstants();

    $list = [];

    foreach ($constants as $constant => $code) {
        $str = str_replace("IS_", "", $constant);

        $str = ucfirst(strtolower($str));

        $list[$code] = $str;
    }

    return $list;
}

function get_class_info($class_name)
{
    if (is_string($class_name)) {
        if (!class_exists($class_name)) {
            throw_exception("class $class_name does not exit");
        }
    } else {
        $class_name = get_class($class_name);
    }

    $class = new ReflectionClass($class_name);
    $file = $class->getFileName();
    $class_short_name = $class->getShortName();
    $all_properties = $class->getProperties();
    $constants = $class->getConstants();
    $all_methods = $class->getMethods();

    $properties = [];
    $property_modifier_list = get_reflection_property_human_list();

    foreach ($all_properties as $property) {
        $modifier = $property->getModifiers();
        $properties[] = [
            "name" => $property->name,
            "modifier" => $property_modifier_list[$modifier] ?? $modifier,
        ];
    }

    $methods = [];

    $method_modifier_list = get_reflection_method_human_list();

    foreach ($all_methods as $method) {
        $modifier = $method->getModifiers();
        $methods[] = [
            "name" => $method->name,
            "modifier" => $method_modifier_list[$modifier] ?? $modifier,
        ];
    }

    return compact("file", "class_name", 'class_short_name', "constants", "properties", "methods");
}


function get_mime_type_from_extension(string $extension): string
{
    $extension = strtolower(ltrim($extension, '.')); // Clean and lowercase the extension

    // A comprehensive, though not exhaustive, list of common MIME types by extension.
    // This list can be expanded as needed.
    $mimeTypes = [
        // Text
        'txt'  => 'text/plain',
        'html' => 'text/html',
        'htm'  => 'text/html',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'csv'  => 'text/csv',
        'php'  => 'application/x-php', // or text/html depending on server config

        // Images
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'bmp'  => 'image/bmp',
        'webp' => 'image/webp',
        'ico'  => 'image/x-icon',
        'svg'  => 'image/svg+xml',
        'tiff' => 'image/tiff',
        'tif'  => 'image/tiff',
        'psd'  => 'image/vnd.adobe.photoshop',

        // Audio
        'mp3'  => 'audio/mpeg',
        'wav'  => 'audio/wav',
        'ogg'  => 'audio/ogg',
        'oga'  => 'audio/ogg',
        'flac' => 'audio/flac',
        'aac'  => 'audio/aac',

        // Video
        'mp4'  => 'video/mp4',
        'mpeg' => 'video/mpeg',
        'mpg'  => 'video/mpeg',
        'webm' => 'video/webm',
        'avi'  => 'video/x-msvideo',
        'mov'  => 'video/quicktime',
        'wmv'  => 'video/x-ms-wmv',
        'flv'  => 'video/x-flv',

        // Documents
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls'  => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt'  => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'odt'  => 'application/vnd.oasis.opendocument.text',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
        'odp'  => 'application/vnd.oasis.opendocument.presentation',
        'rtf'  => 'application/rtf',

        // Archives
        'zip'  => 'application/zip',
        'rar'  => 'application/x-rar-compressed',
        'tar'  => 'application/x-tar',
        'gz'   => 'application/gzip',
        '7z'   => 'application/x-7z-compressed',

        // Fonts
        'ttf'  => 'font/ttf',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'eot'  => 'application/vnd.ms-fontobject',
        'otf'  => 'font/otf',

        // Web/Application
        'xml'  => 'application/xml',
        'apk'  => 'application/vnd.android.package-archive',
        'dmg'  => 'application/x-apple-diskimage',
        'exe'  => 'application/x-ms-dos-executable',
        'bin'  => 'application/octet-stream',
        'dll'  => 'application/x-msdownload',
    ];

    // Return the MIME type if found, otherwise return a generic binary stream type.
    return $mimeTypes[$extension] ?? 'application/octet-stream';
}


function array_json_normatlized($array)
{
    foreach ($array as $key => $value) {
        if (is_object($value)) {
            $value = (array) $value;
        }
        if (is_array($value)) {
            $array[$key] = array_json_normatlized($value);
        } else if (is_string($value)) {
            $array[$key] = stripslashes($value);
        }
    }

    return $array;
}

function my_json_encode($array, $options = [])
{
    $default_options = [
        'halt_on_error' => true,
    ];

    $options = array_merge($default_options, $options);

    $array = array_json_normatlized($array, $options);

    $json = json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
        if ($options['halt_on_error']) {
            throw_exception("Error encoding JSON: " . json_last_error_msg());
        }
    }

    return $json;
}

function decode_potentially_double_escaped_json($jsonString, $assoc = false, $depth = 512, $flags = 0)
{
    // Attempt to decode directly first.
    // This covers cases where the JSON string is already correctly escaped.
    $decoded = json_decode($jsonString, $assoc, $depth, $flags);

    // If initial decode fails and the string contains escaped double quotes,
    // it might be double-escaped. Try stripping slashes.
    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
        // If the string starts and ends with a double quote that is also escaped
        // this is a strong indication of double escaping.
        // For example: "{\"key\":\"value\"}"
        // The outer quotes and inner escaped quotes need stripping.
        $tempString = $jsonString;
        if (substr($tempString, 0, 1) === '"' && substr($tempString, -1, 1) === '"') {
            // Remove the outer quotes, as stripslashes won't touch them directly.
            $tempString = substr($tempString, 1, -1);
        }

        $strippedString = stripslashes($tempString);
        $decoded = json_decode($strippedString, $assoc, $depth, $flags);
    }

    return $decoded;
}

function my_json_decode($str)
{
    return decode_potentially_double_escaped_json($str);
}

function array_escape_for_sql($str)
{
    if (is_array($str)) { // Handle arrays recursively if necessary (CI's insert/update handles this)
        foreach ($str as $key => $val) {
            $str[$key] = array_escape_for_sql($val);
        }
        return $str;
    }
    // This is a simplified mock. In a real scenario, this would use the actual DB connection.
    // For a true demonstration of escaping without a live DB, it's hard.
    // We'll just replace problematic characters for illustrative purposes.
    $escaped_str = str_replace(
        ["\\", "\0", "\n", "\r", "\x1a", "'", '"'],
        ["\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'],
        $str
    );
    return $escaped_str;
}

function str_get_first_chars($string)
{
    $string = str_convert_multi_space_to_single_space($string);
    $words = explode(' ', $string); // Split the string by space
    $firstChars = [];

    foreach ($words as $word) {
        if (!empty($word)) { // Ensure the word is not empty (handles multiple spaces)
            $firstChars[] = $word[0]; // Get the first character (index 0)
        }
    }

    return $firstChars;
}

function str_convert_multi_space_to_single_space($string)
{
    $string = preg_replace('/\s+/', ' ', $string);
    $string = trim($string);
    return $string;
}

function array_find_duplicates(array $array): array
{
    // Count the occurrences of each value in the array
    $counts = array_count_values($array);

    // Filter the counts to keep only values that appear more than once
    $duplicates = array_filter($counts, function ($count) {
        return $count > 1;
    });

    // Return the keys of the filtered array, which are the duplicate values
    return array_keys($duplicates);
}

function array_check_data_type_and_throw_error(array $array, array $valid_data_types, bool $recursive = false, $msg = "", $msg_prefix = "")
{
    if (!$msg) {
        $msg = "Key : {key} : data type is {data_type}, only {valid_data_types} are allowed";
    }

    foreach ($array as $k => $v) {

        if ($recursive && is_array($v)) {
            array_check_data_type_and_throw_error($v, $valid_data_types, $recursive, $msg, $msg_prefix . "->" . $k);
        } else {
            $data_type = gettype($v);

            if (!in_array($data_type, $valid_data_types)) {
                $msg = str_replace("{key}", $msg_prefix . $k, $msg);

                $msg = str_replace("{data_type}", $data_type, $msg);

                $msg = str_replace("{valid_data_types}", implode(",", $valid_data_types), $msg);

                throw new Exception($msg);
            }
        }
    }
}


function array_check_value_is_scaler_and_throw_error(array $array, bool $recursive = false, $msg = "", $msg_prefix = "")
{
    if (!$msg) {
        $msg = "Key : {key} : variable should be scaler";
    }

    foreach ($array as $k => $v) {
        if ($recursive) {
            if (is_object($v)) {
                $v = (array) $v;
            }
        }

        if ($recursive && is_array($v)) {
            array_check_value_is_scaler_and_throw_error($v, $recursive, $msg, $msg_prefix . "->" . $k);
        } else {
            if (!is_scalar($v)) {
                $msg = str_replace("{key}", $msg_prefix . $k, $msg);

                throw new Exception($msg);
            }
        }
    }
}

function array_except(array $array, array $except_values, bool $recursive = false)
{
    array_check_value_is_scaler_and_throw_error($except_values);

    foreach ($array as $k => $v) {
        if ($recursive && is_array($v)) {
            $array[$k] = array_except($v, $except_values);
        } else {
            foreach ($except_values as $except_value) {
                if ($v == $except_value) {
                    unset($array[$k]);
                }
            }
        }
    }

    return $array;
}

function array_human_keys(array $array, bool $recursive = false)
{
    $ret_array = [];
    foreach ($array as $k => $v) {
        $k = strtolower($k);
        $k = str_function_name_to_human_text($k);
        $k = ucwords($k);

        if ($recursive && is_array($v)) {
            $ret_array[$k] = array_human_keys($v);
        } else {
            $ret_array[$k] = $v;
        }
    }

    return $ret_array;
}


function str_path_normalize_to_save_to_data_base($path)
{
    $path = str_convert_multi_space_to_single_space($path);
    $path = str_replace(" ", "-", $path);
    $path = str_replace("\\", "/", $path);
    $path = trim($path, "/");

    return $path;
}

function str_normalize_for_array_key($str)
{
    $str = str_convert_multi_space_to_single_space($str);
    $str = str_replace(" ", "-", $str);
    $str = preg_replace("/[^a-zA-Z0-9.-]/", "", $str);
    $str = trim($str);

    return $str;
}

function date_get_for_file_name()
{
    $dt = new DateTime();

    return $dt->format("Y-m-d_H-i-s");
}


function is_developer_role_login()
{
    $auth_user = Auth::user();

    if ($auth_user) {
        return $auth_user->hasRole(Role::TYPE_DEVELOPER);
    }

    return false;
}

function is_admin_role_login()
{
    $auth_user = Auth::user();

    if ($auth_user) {
        return $auth_user->hasRole(Role::TYPE_SYSTEM_ADMIN);
    }

    return false;
}

function get_value_from_list($list, $find_key, $default = "")
{
    if (is_null($find_key))
    {
        return $default;
    }

    if (!is_array($find_key))
    {
        if (!is_scalar($find_key))
        {
            throw new UserException("arg 2 : key should be scaler or array, found : " . gettype($find_key));
        }
        $keys = [$find_key];
    }
    else
    {
        $keys = $find_key;
    }

    foreach($keys as $key)
    {
        $key = trim($key);

        if (isset($list[$key])) {
            return $list[$key];
        }
    }

    foreach($list as $k => $v)
    {
        foreach($keys as $key)
        {
            if (trim(strtolower($k)) == trim(strtolower($key)))
            {
                return $v;
            }
        }
    }

    return $default;
}

function get_modified_time_of_file_or_dir($filePath)
{
    if ($filePath && strlen($filePath) > 0) {
        if (file_exists($filePath)) {
            $timestamp = filemtime($filePath);

            if ($timestamp !== false) {
                return DateUtility::getDate($timestamp);
            } else {
                throw new UserException("fail to find modified time of $filePath", UserException::ERROR_PHP_FAIL);
            }
        } else {
            throw new UserException("file does exist : $filePath");
        }
    }

    return null;
}

function get_asset_url($file)
{
    $new_file = explode("?", $file)[0];
    $modified_date_time = get_modified_time_of_file_or_dir($new_file);
    if ($modified_date_time) {
        $modified_date_time = DateUtility::getDate($modified_date_time, DateUtility::DATETIME_OUT_FORMAT_FILE);
        return asset($new_file . "?t=" . $modified_date_time);
    } else {
        return $file;
    }
}

function str_short_with_dot($str, $max_string_len = 45)
{
    if ($str)
    {
        if (strlen($str) > $max_string_len)
        {
            $str = substr($str, 0, $max_string_len) . "....";
        }
    }

    return $str;
}

function array_short_string_with_dot(array $arr, $max_string_len = 45, bool $recursive = true)
{
    foreach ($arr as $k => $v) {
        if (is_string($v)) {

            $arr[$k] = str_short_with_dot($v, $max_string_len);

        } else if (is_array($v) && $recursive) {
            $arr[$k] = array_short_string_with_dot($v, $max_string_len, $recursive);
        }
    }

    return $arr;
}

function array_purge_if_exceed_limit(array $arr, $max_len = 100, $recursive = true)
{
    $count = count($arr);

    $keys = array_keys($arr);

    for ($i = $max_len + 1; $i <= $count; $i++) {
        if (isset($keys[$i])) {
            unset($arr[$keys[$i]]);
        }
    }

    foreach ($arr as $k => $v) {
        if (is_object($v)) {
            $v = (array) $v;
        }

        if (is_array($v) && $recursive) {
            $arr[$k] = array_purge_if_exceed_limit($v, $max_len, $recursive);
        }
    }

    return $arr;
}


function is_ajax_request()
{
    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        return true;
    }
    return false;
}

function array_get_scaler_value_only(array $data)
{
    $list = [];

    foreach($data as $k => $v)
    {
        if (is_scalar($v))
        {
            $list[$k] = $v;
        }
    }

    return $list;
}

function get_value_from_object($obj, $keys, $default = "")
{
    if (!is_object($obj))
    {
        return $default;
    }

    foreach($keys as $key)
    {
        if ($obj->{$key})
        {
            return $obj->{$key};
        }
    }

    return $default;
}

function str_space_after_every_captial_letter(string $string): string
{
    // The regex `/(?=[A-Z])/` looks for a position that is immediately
    // followed by an uppercase letter (positive lookahead).
    // We replace this position with a space.
    return preg_replace('/(?=[A-Z])/', ' ', $string);
}

function check_string_is_date_time(string $str, string $format = "Y-m-d H:i:s")
{
    $sub_value = $str;

    if (strpos($str, "T") !== false)
    {
        $sub_value = substr($str, 0, strpos($str, "T"));
        $format = substr($format, 0, strpos($format, "T"));
    }
    else if (strpos($str, " ")  !== false)
    {
        $sub_value = substr($str, 0, strpos($str, " "));
        $format = substr($format, 0, strpos($format, " "));
    }

    if ($format && $sub_value)
    {
        $dateTime = \DateTime::createFromFormat($format, $sub_value);

        if ($dateTime && $dateTime->format($format) === $sub_value)
        {
            return true;
        }
    }

    return false;
}