<?php

namespace App\Helpers;

<<<<<<< HEAD
use Exception;
=======
use App\Exceptions\UserException;
use DateTime;
use Exception;
use Illuminate\Filesystem\LocalFilesystemAdapter;
>>>>>>> base_v3

class FileUtility
{
    const use_s3 = false;
<<<<<<< HEAD
    public $maxSize, $ext, $path, $errors;
    public $filename, $extension, $file;
    // static variables
    public static $FIRST = 1, $LAST = 2;
    /**
     * Constructor
     * 
     * @param int $maxSize
     * @param array $extensions
     * @param array $options
     */
    public function __construct($maxSize, $extensions = array())
    {
        $this->maxSize = $maxSize;
        $this->ext = $extensions;

        foreach ($this->ext as $k => $ext) {
            $this->ext[$k] = strtolower(trim($ext));
        }
    }

    /**
     * 
     * upload a file to destination
     * @param array $file
     * @param string $dest_path
     * @return boolean
     */
    public function uploadFile($file, $dest_path, $filename = "")
    {
        //validating file
        $this->errors = array();
        $file["name"] = trim($file["name"]);
        if (!$this->validateFile($file)) {
            return false;
        }

        //creating folder
        $dest_path = static::removePathSlashs($dest_path);
        $dest_path .= "/";

        self::createFolder($dest_path);

        $this->path = $dest_path;

        $temp = pathinfo($file["name"]);

        $this->filename = static::cleanFileName($temp['filename']);
        $this->extension = $temp['extension'];

        if ($filename) {
            $this->filename = static::cleanFileName($filename);
            if (strlen($this->filename) > 100) {
                $this->filename = substr($this->filename, 0, 100);
            }

            $this->file = $this->filename . "." . $this->extension;
        } else {
            if (strlen($this->filename) > 100) {
                $this->filename = substr($this->filename, 0, 100);
            }

            $this->file = self::getAutoincreamentFileName($this->filename, $this->extension, $dest_path);
            $this->filename = pathinfo($this->file, PATHINFO_FILENAME);
        }

        return move_uploaded_file($file['tmp_name'], $this->path . $this->file);
    }

    /**
     * validate the file
     * @param string $file
     * @return boolean
     */
    public function validateFile($file)
    {
        $result = true;

        if ($file['size'] > $this->maxSize) {
            $this->errors[] = "File size must not exceeds " . round($this->maxSize / 1024) . " kb";
            $result = false;
        }

        $temp = pathinfo($file["name"]);

        $this->filename = $temp['filename'];
        if (!isset($temp['extension']) || !$temp['extension']) {
            $this->errors[] = "Could not find type of file";
            $result = false;
        } else {
            $this->extension = strtolower($temp['extension']);

            if (!empty($this->ext) && !in_array($this->extension, $this->ext)) {
                $this->errors[] = "Invalid file Type : " . $this->extension;
                $result = false;
            }
        }

        return $result;
    }

    /**
     * return filename which which will be save 
=======

    public function __construct(protected LocalFilesystemAdapter $disk) {}

    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * return filename which which will be save
>>>>>>> base_v3
     * @param string $filename
     * @param string $ext
     * @param string $dest_path
     * @return string
     */
    public static function getAutoincreamentFileName($filename, $ext, $dest_path, $sep = "_", $i = 0)
    {
<<<<<<< HEAD
        $temp_name = $i > 0 ? $filename . $sep . $i : $filename;
=======
        $temp_name = $i > 0 ? ($filename . $sep . $i) : $filename;
>>>>>>> base_v3

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
<<<<<<< HEAD
=======
        if (!(file_exists($path) && is_dir($path)))
        {
            throw new UserException("path not found : $path");
        }

>>>>>>> base_v3
        $files = scandir($path);
        $ret_files = array();

        foreach ($files as $k => $file) {
<<<<<<< HEAD
            $f = $path . $file . "/";
=======
            $f = $path . "/" . $file;
>>>>>>> base_v3

            if ($file == '.' || $file == '..') {
            } else if ($recursive && is_dir($f)) {
                $ret_files = array_merge($ret_files, self::getFileList($f, $exts, $recursive));
            } else if (!empty($exts) && !in_array(pathinfo($file, PATHINFO_EXTENSION), $exts)) {
            } else {
<<<<<<< HEAD
                $ret_files[] = $path . $file;
=======
                $ret_files[] = $f;
            }
        }

        return $ret_files;
    }

    public static function getFolderList($path, $prefix = "", bool $recursive = false)
    {
        $files = scandir($path);

        $ret_files = array();

        foreach ($files as $k => $file) {
            $f = $path . "/" . $file;

            if ($file == '.' || $file == '..') {
            } else if (is_dir($f)) {

                $ret_files[] = $prefix . $file;

                if ($recursive)
                {
                    $dir_name = pathinfo($file, PATHINFO_FILENAME);
                    $ret_files = array_merge($ret_files, self::getFolderList($f, $dir_name . "/"));
                }
>>>>>>> base_v3
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

<<<<<<< HEAD
        $path = "";

        if ($file) 
        {
=======
        $parsed_url = parse_url($file);

        if (isset($parsed_url['scheme']) && isset($parsed_url['host']) && isset($parsed_url['host'])) {
            return $file;
        }


        $path = "";

        if ($file) {
>>>>>>> base_v3
            $file_without_query_string = explode("?", $file)[0];

            if (file_exists($file_without_query_string)) {
                $path = $file;
<<<<<<< HEAD
            }
            else
            {
                if (self::use_s3) 
                {
                    $aws = new AWSFileUtility();
        
                    if ($aws->isExist($file_without_query_string)) 
                    {
=======
            } else {
                if (self::use_s3) {
                    $aws = new AWSFileUtility();

                    if ($aws->isExist($file_without_query_string)) {
>>>>>>> base_v3
                        return AWSFileUtility::get($file_without_query_string);
                    }
                }
            }
        }

<<<<<<< HEAD
        if (empty($path) && $default) 
        {
=======
        if (empty($path) && $default) {
>>>>>>> base_v3
            $path = "img/dummy.jpg";
        }

        $path = str_replace("\\", "/", $path);

        $path = trim($path, "/");

<<<<<<< HEAD
        if ($prepend_domain && $path)
        {
=======
        if ($prepend_domain && $path) {
>>>>>>> base_v3
            return url('/') . "/" . $path;
        }

        return "/" . $path;
    }

<<<<<<< HEAD
    public static function delete($file, $aws = true)
    {
        $file = explode("?", $file)[0];

        if (file_exists($file)) {
            if (!unlink($file)) {
                return false;
=======
    public function delete($file, $aws = true)
    {
        $file = trim($file);
        $file = explode("?", $file)[0];

        if ($this->disk->fileExists($file)) 
        {
            if (!$this->disk->delete($file)) {
                throw new UserException("Fail To Delete File $file", UserException::ERROR_LARAVEL_FAIL);
>>>>>>> base_v3
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

<<<<<<< HEAD
    public static function isExist($file)
=======
    public function isExist($file)
>>>>>>> base_v3
    {
        $file = trim($file);
        $file = explode("?", $file)[0];

<<<<<<< HEAD
        if (!$file) {
=======
        if (!$this->disk->fileExists($file)) {
>>>>>>> base_v3
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
<<<<<<< HEAD
        if ( !file_exists($src) )
        {
            throw new Exception("Src : $src is not exist");
        }

        if ( is_dir($src) )
        {
            throw new Exception("Src : $src is a directroy. only file can move");
        }

        $dest_path = dirname($dest);        

        if ( file_exists($dest) )
        {
            if (!$will_override)
            {
=======
        if (!file_exists($src)) {
            throw new Exception("Src : $src is not exist");
        }

        if (is_dir($src)) {
            throw new Exception("Src : $src is a directroy. only file can move");
        }

        $dest_path = dirname($dest);

        if (file_exists($dest)) {
            if (!$will_override) {
>>>>>>> base_v3
                $filename = self::cleanFileName(pathinfo($dest, PATHINFO_FILENAME));
                $ext = pathinfo($dest, PATHINFO_EXTENSION);

                $file = self::getAutoincreamentFileName($filename, $ext, $dest_path);

                $dest = $dest_path . "/" . $file;
            }
<<<<<<< HEAD
        }
        else
        {
            $ext = pathinfo($dest, PATHINFO_EXTENSION);

            if (!$ext)
            {
                throw new Exception("Dest : $dest have no extension");
            }
            
=======
        } else {
            $ext = pathinfo($dest, PATHINFO_EXTENSION);

            if (!$ext) {
                throw new Exception("Dest : $dest have no extension");
            }

>>>>>>> base_v3
            self::createFolder($dest_path);
        }

        if (rename($src, $dest)) {
            return $dest;
        }

        return false;
    }

<<<<<<< HEAD
    public static function removePathSlashs($path, $side = '')
    {
        $side = strtoupper($side);
        $path = trim(str_replace('\\', '/', $path));

        if ($side == 'FIRST' || $side == 'START' || empty($side)) {
            if (substr($path, 0, 1) == "/") {
                $path = substr($path, 1, strlen($path));
            }
        }

        if ($side == 'LAST' || $side == 'END' || empty($side)) {
            if (substr($path, -1) == "/") {
                $path = substr($path, 0, strrpos($path, "/"));
            }
        }
        return $path;
    }

=======
>>>>>>> base_v3
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

<<<<<<< HEAD
        if (count($arr) > 1)
        {
            $base64 = $arr[1];
        }
        else
        {
=======
        if (count($arr) > 1) {
            $base64 = $arr[1];
        } else {
>>>>>>> base_v3
            $base64 = $arr[0];
        }

        $ifp = fopen($file, 'wb');

        fwrite($ifp, base64_decode($base64));

        // clean up the file resource
        fclose($ifp);

        return $file;
    }
<<<<<<< HEAD
=======


    public function writeFile(string $filepath, $contents, $options = [])
    {
        if (!$this->disk->put($filepath, $contents)) {
            throw_exception("Fail To Write File at $filepath");
        }

        return false;
    }

    public function writeContent(string $path, string $file, $contents)
    {
        $path = str_path_normalize_to_save_to_data_base($path);

        return $this->writeFile($path . "/" . $file, $contents);
    }

    public function getContent(string $filepath)
    {
        $contents = $this->disk->get($filepath);

        return $contents;
    }

    public function getUrl($file)
    {
        $root_path = $this->disk->path("/");

        if ($this->disk->fileExists($file)) {

            if (strpos($root_path, "/hdd/erp_test") !== false)
            {
                //external
                return "/storage/erp_test/" . $file;
            }
            else
            {
                return "/storage/" . $file;
            }
        }

        return null;
    }

    public static function getFileTypeFromExt($ext)
    {
        $ext_types = [
            'js'   => 'javascript',
            'json' => 'json',
            'xml'  => 'xml',
            'csv'  => 'csv',
            'php'  => 'php',
            'psd'  => 'psd',
            'pdf'  => 'pdf',
            'doc'  => 'doc',
            'docx' => 'docx',
            'xls'  => 'xls',
            'xlsx' => 'xlsx',
            'ppt'  => 'ppt',
            'pptx' => 'pptx',
            'odt'  => 'document',
            'ods'  => 'spreadsheet',
            'odp'  => 'presentation',
            'zip'  => 'zip',
            'rar'  => 'rar',
            'tar'  => 'tar',
            'gz'   => 'gzip',
            '7z'   => '7z',
            'apk'  => 'apk',
            'dmg'  => 'dmg',
            'exe'  => 'exe',
            'bin'  => 'bin',
            'dll'  => 'dll',
        ];

        if (isset($ext_types[$ext]))
        {
            return $ext_types[$ext];
        }

        $mime_type = get_mime_type_from_extension($ext);

        $arr = explode("/", $mime_type);

        return $arr[0];
    }

    public static function getFontAwesomeOnType($type)
    {
        switch($type)
        {
            case "csv";
                return "fa-solid fa-file-csv";
            break;

            case "pdf";
                return "fa-solid fa-file-pdf";
            break;

            case "xls";
            case "xlsx";
                return "fa-solid fa-file-excel";
            break;

            case "doc";
            case "docx";            
                return "fa-solid fa-file-word";
            break;

            case "ppt":
            case "pptx":
                return "fa-solid fa-file-ppt";
                break;

            case "document";
                return "fa-solid fa-file-doc";
                break;

            case "spreadsheet":
            case "csv":
                return "fa-solid fa-file-spreadsheet";
                break;
            
            case "presentation":
                return "fa-solid fa-file-powerpoint";
                break;

            case "zip":
            case "rar":
            case "tar":
            case "gzip":
            case "7z":
                return "fa-solid fa-file-zip";
                break;

            case "bin":
                return "fa-solid fa-square-binary";
                break;

            case "text":
                return "fa-solid fa-file-lines";
                break;

            case "audio":
                return "fa-solid fa-file-audio";
                break;

            case "video":
                return "fa-solid fa-file-video";
                break;

            case "image":
                return "fa-solid fa-file-image";
                break;
        }

        return "fa-solid fa-file";
    }

    public static function getFilesAndFolderOlderthan(String $path, $last_datetime, array $exts = [], bool $recursive = false)
    {
        if (!(file_exists($path) && is_dir($path)))
        {
            throw new UserException("path not found : $path");
        }

        $files = scandir($path);
        $ret = [
            "folders" => [],
            "files" => [],
        ];

        foreach ($files as $k => $file) {
            $f = $path . "/" . $file;

            if ($file == '.' || $file == '..')
            {
            }
            else if (is_dir($f))
            {
                if (DateUtility::compare($last_datetime, get_modified_time_of_file_or_dir($f)) > 0)
                {
                    $ret['folders'][] = $f;
                }
            }

            if ($file == '.' || $file == '..') 
            {
            } 
            else if ($recursive && is_dir($f)) 
            {
                $ret2 = self::getFilesAndFolderOlderthan($f, $last_datetime, $exts, $recursive);

                $ret = array_merge_recursive($ret2, $ret);
            } 
            else if (!empty($exts) && !in_array(pathinfo($file, PATHINFO_EXTENSION), $exts)) 
            {
            } 
            else if (!is_dir($f))
            {
                if (DateUtility::compare($last_datetime, get_modified_time_of_file_or_dir($f)) > 0)
                {
                    $ret['files'][] = $f;
                }

            }
        }

        return $ret;
    }
>>>>>>> base_v3
}
