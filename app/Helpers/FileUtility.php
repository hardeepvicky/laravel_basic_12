<?php

namespace App\Helpers;

use Exception;
use Illuminate\Filesystem\LocalFilesystemAdapter;

class FileUtility
{
    const use_s3 = false;

    public function __construct(protected LocalFilesystemAdapter $disk) {}

    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * return filename which which will be save 
     * @param string $filename
     * @param string $ext
     * @param string $dest_path
     * @return string
     */
    public static function getAutoincreamentFileName($filename, $ext, $dest_path, $sep = "_", $i = 0)
    {
        $temp_name = $i > 0 ? $filename . $sep . $i : $filename;

        if (file_exists($dest_path . $temp_name . "." . $ext)) {
            return self::getAutoincreamentFileName($filename, $ext, $dest_path, $sep, $i + 1);
        } else {
            return $temp_name . "." . $ext;
        }
    }

    public static function createFolder($path)
    {
        if (!file_exists($path)) {
            if (!mkdir($path, 0777, TRUE)) {
                throw new Exception("Fail to create $path");
            }
        } else {
            if (!is_readable($path)) {
                throw new Exception("$path is not readable");
            }
        }
    }

    public static function deleteAll($path, $exts = array(), $recursive = false)
    {
        $files = self::getFileList($path, $exts, $recursive);

        $result = true;
        foreach ($files as $file) {
            if (unlink($file)) {
                //continue
            } else {
                $result = false;
            }
        }

        return $result;
    }

    public static function getFileList($path, $exts = array(), $recursive = false)
    {
        $path = trim(trim($path), "/") . "/";

        $files = scandir($path);
        $ret_files = array();

        foreach ($files as $k => $file) {
            $f = $path . $file . "/";

            if ($file == '.' || $file == '..') {
            } else if ($recursive && is_dir($f)) {
                $ret_files = array_merge($ret_files, self::getFileList($f, $exts, $recursive));
            } else if (!empty($exts) && !in_array(pathinfo($file, PATHINFO_EXTENSION), $exts)) {
            } else {
                $ret_files[] = $path . $file;
            }
        }

        return $ret_files;
    }

    public static function getFolderList($path)
    {
        $path = trim(trim($path), "/") . "/";
        $files = scandir($path);

        $ret_files = array();

        foreach ($files as $k => $file) {
            $f = $path . $file;

            if ($file == '.' || $file == '..') {
            } else if (is_dir($f)) {
                $ret_files[] = $file;
            }
        }

        return $ret_files;
    }

    public static function read($file)
    {
        $file = explode("?", $file)[0];

        if (file_exists($file)) {
            return array(
                "filename" => pathinfo($file, PATHINFO_BASENAME),
                "content_type" => mime_content_type($file),
                "content" => file_get_contents($file)
            );
        }

        if (self::use_s3) {
            $aws = new AWSFileUtility();
            $result = $aws->read($file);

            if ($result == false) {
                return false;
            }

            return array(
                "filename" => pathinfo($file, PATHINFO_BASENAME),
                "content_type" => $result["ContentType"],
                "content" => $result["Body"]
            );
        }

        return false;
    }

    public static function get($file, $default = true, $prepend_domain = true)
    {
        $file = trim($file);

        $parsed_url = parse_url($file);

        if (isset($parsed_url['scheme']) && isset($parsed_url['host']) && isset($parsed_url['host'])) {
            return $file;
        }


        $path = "";

        if ($file) {
            $file_without_query_string = explode("?", $file)[0];

            if (file_exists($file_without_query_string)) {
                $path = $file;
            } else {
                if (self::use_s3) {
                    $aws = new AWSFileUtility();

                    if ($aws->isExist($file_without_query_string)) {
                        return AWSFileUtility::get($file_without_query_string);
                    }
                }
            }
        }

        if (empty($path) && $default) {
            $path = "img/dummy.jpg";
        }

        $path = str_replace("\\", "/", $path);

        $path = trim($path, "/");

        if ($prepend_domain && $path) {
            return url('/') . "/" . $path;
        }

        return "/" . $path;
    }

    public static function delete($file, $aws = true)
    {
        $file = explode("?", $file)[0];

        if (file_exists($file)) {
            if (!unlink($file)) {
                return false;
            }
        }

        if (self::use_s3 && $aws) {
            $aws = new AWSFileUtility();
            if ($aws->isExist($file)) {
                if (!$aws->delete($file)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function isExist($file)
    {
        $file = trim($file);
        $file = explode("?", $file)[0];

        if (!$file) {
            return false;
        }

        if (file_exists($file)) {
            return true;
        } else if (self::use_s3) {
            $aws = new AWSFileUtility();
            return $aws->isExist($file);
        }

        return false;
    }

    public static function move($src, $dest, bool $will_override = false)
    {
        if (!file_exists($src)) {
            throw new Exception("Src : $src is not exist");
        }

        if (is_dir($src)) {
            throw new Exception("Src : $src is a directroy. only file can move");
        }

        $dest_path = dirname($dest);

        if (file_exists($dest)) {
            if (!$will_override) {
                $filename = self::cleanFileName(pathinfo($dest, PATHINFO_FILENAME));
                $ext = pathinfo($dest, PATHINFO_EXTENSION);

                $file = self::getAutoincreamentFileName($filename, $ext, $dest_path);

                $dest = $dest_path . "/" . $file;
            }
        } else {
            $ext = pathinfo($dest, PATHINFO_EXTENSION);

            if (!$ext) {
                throw new Exception("Dest : $dest have no extension");
            }

            self::createFolder($dest_path);
        }

        if (rename($src, $dest)) {
            return $dest;
        }

        return false;
    }

    public static function cleanFileName($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-_]/', '', $string); // Removes special chars.
    }

    public static function base64ToFile(String $base64, String $dest_path, String $file): String
    {
        $dest_path = Util::removePathSlashs($dest_path);

        self::createFolder($dest_path);

        $filename = self::cleanFileName(pathinfo($file, PATHINFO_FILENAME));
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        $file = self::getAutoincreamentFileName($filename, $ext, $dest_path);

        $file = $dest_path . "/" . $file;

        $arr = explode(',', $base64);

        if (count($arr) > 1) {
            $base64 = $arr[1];
        } else {
            $base64 = $arr[0];
        }

        $ifp = fopen($file, 'wb');

        fwrite($ifp, base64_decode($base64));

        // clean up the file resource
        fclose($ifp);

        return $file;
    }


    public function writeFile(string $filepath, $contents, $options = [])
    {
        if (!$this->disk->put($filepath, $contents)) {
            throw_exception("Fail To Write File at $filepath");
        }

        return false;
    }

    public function writeLog(string $path, string $file, $contents)
    {
        $path = str_path_normalize_to_save_to_data_base($path);

        return $this->writeFile($path . "/" . $file, $contents);
    }

    public function getContent(string $filepath)
    {
        $filepath = str_path_normalize_to_save_to_data_base($filepath);

        $contents = $this->disk->get($filepath);

        return $contents;
    }

    public function getUrl($file)
    {
        if ($this->disk->fileExists($file)) {
            return route("get_file_url", ["file_path" => urlencode($file)]);
        }

        return null;
    }
}
