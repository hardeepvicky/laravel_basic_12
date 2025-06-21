<?php

use App\Helpers\DateUtility;
use App\Helpers\Util;


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
    if (!env("APP_DEBUG")) {
        return;
    }

    $callBy = debug_backtrace()[0];
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

    if ($will_exit) {
        exit;
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
        return DateUtility::getDate($datetime, DateUtility::DATETIME_OUT_FORMAT, 'Asia/Kolkata');
    }

    return "";
}

function if_date($datetime)
{
    if ($datetime) {
        return DateUtility::getDate($datetime, DateUtility::DATE_OUT_FORMAT, 'Asia/Kolkata');
    }

    return "";
}

function download_start($file, $content_type, $delete_after_download = true)
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
    if (strpos($class_name, "\\") >= 0) {
        $arr = explode("\\", $class_name);

        if ($arr) {
            $class_name = end($arr);
        }
    }

    return $class_name;
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
            $msg = str_replace("{key}", "key : " . $key, $msg);
            throw_exception($msg);
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
            $msg = str_replace("{key}", "key : " . $key, $msg);
            throw_exception($msg);
        }
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

function array_extract_only_of_keys(array $arr, array $keys, bool $throw_exception_on_key_not_found = false)
{
    $ret_arr = [];

    foreach ($keys as $key) {
        if (isset($arr[$key])) {
            $ret_arr[$key] = $arr[$key];
        } else {
            if ($throw_exception_on_key_not_found) {
                throw_exception("$key not found in arr");
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

    return compact("file", 'class_short_name', "constants", "properties", "methods");
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
