<?php

namespace App\Console\Commands;

use App\Exceptions\UserException;
use App\Helpers\FileUtility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DbRestoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore For Mysql And Mongodb';

    public static string $path = "files/backup";

    public FileUtility $fileUtility;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $db_config_list = config("database.connections");

        $options = $flip_options = [];

        foreach ($db_config_list as $key => $arr) {
            $name =  $arr['database'] . " (" . $arr['driver'] . ")";
            $options[] = $name;
            $flip_options[$name] = $key;
        }

        if (!$options) {
            $this->error("No Database Config found");
            return;
        }

        $choice = $this->choice(
            'Select Database :',
            $options,
        );

        if (!$choice) {
            return;
        }

        $db_config = $db_config_list[$flip_options[$choice]];

        $this->fileUtility = new FileUtility(self::getStorageDisk());

        if ($db_config['driver'] == "mysql") {
            $this->restore_mysql($db_config);
        }

        return true;
    }

    public function restore_mysql($db_config)
    {
        $this->info('Database restore Started');

        $path = $this->fileUtility->getDisk()->path(self::$path);

        $folder_list = FileUtility::getFolderList($path);

        if (empty($folder_list)) {
            $this->info("there is no any backup folder at $path");
            return;
        }

        $choice = $this->choice(
            'Select Folder :',
            $folder_list,
        );

        if (!$choice) {
            return;
        }

        $sub_folder = $choice;

        $path = $path . "/" . $sub_folder;

        $folder_list = FileUtility::getFolderList($path);

        $choice = $this->choice(
            'Select Folder :',
            $folder_list,
        );

        if (!$choice) {
            return;
        }

        $sub_folder = $choice;

        $path = $path . "/" . $sub_folder;

        $files = FileUtility::getFileList($path, ["sql"], true);

        if (empty($files)) {
            $this->info("No Sql file found");
            return;
        }

        $options = ["Full Restore", "Structure Restore Only", "Data Restore Only"];

        $choice = $this->choice(
            'Select :',
            $options,
        );

        if (!$choice) {
            return;
        }

        $db_name = $db_config['database'];

        if ($choice == "Full Restore" || $choice == "Structure Restore Only") {
            foreach ($files as $k => $file) {
                if (strpos($file, $db_name) !== false) {
                    dd($file);
                    unset($files[$k]);
                }
            }
        }


        if ($choice == "Full Restore" || $choice == "Data Restore Only") {

            $choice = $this->choice(
                'Select :',
                $options,
            );

            if (!$choice) {
                return;
            }

            dd($files);
        }


        $path .= $folder_count . "/";

        FileUtility::createFolder($path);

        $db_name = $db_config['database'];

        $file = $path . $db_name . "_st.sql";

        if (file_exists($file)) {
            if (!unlink($file)) {
                throw new UserException("Fail to delete file : $file");
            }
        }

        $this->info("DB Structure dumping..");

        $command = 'mysqldump -P' . $db_config['port'] . ' -u' . $db_config['username'] . ' -p' . $db_config['password'] . ' -d --routines --triggers --no-tablespaces --single-transaction ' . $db_name . ' > "' . $file . '"';

        exec($command, $output, $return_var);

        if ($return_var == 2) {
            $this->error("$command is not working.");
            return false;
        }

        $this->info("✅DB Structure Done");

        $zip = new ZipArchive();

        $zip_file = $path . $db_name . ".zip";

        if (file_exists($zip_file)) {
            unlink($zip_file);
            $this->info("Old Zip Backup File Deleted");
        }

        if ($zip->open($zip_file, ZIPARCHIVE::CREATE) != true) {
            $this->error("Could not open archive");
            return false;
        }

        $filename = pathinfo($file, PATHINFO_BASENAME);
        $zip->addFile($file, $filename);

        $this->info("Quering to Fetch All Tables from Database");

        $q = "SHOW FULL TABLES FROM $db_name WHERE Table_Type = 'BASE TABLE'";
        $temp = DB::select($q);

        $count = count($temp);

        $this->info("Table Count : " . $count);

        $data_path = $path . "data/";
        FileUtility::createFolder($data_path);

        $ignore_tables = array();

        $table_sql_list = [];

        $this->info("Dumping Data...");

        foreach ($temp as $arr) {
            $table = array_values((array)$arr)[0];

            if (in_array($table, $ignore_tables)) {
                continue;
            }

            $table_sql_list[$table] = $table;

            $file = $data_path . $table . ".sql";

            if (file_exists($file)) {
                unlink($file);
            }

            $command = 'mysqldump -P' . $db_config['port'] . ' -u' . $db_config['username'] . ' -p' . $db_config['password'] . ' --no-create-info ' . $db_name . ' ' . $table . ' > "' . $file . '"';

            exec($command, $output, $return_var);

            if ($return_var == 2) {
                $this->error("$command is not working.");
            } else {
                $this->info($table);
                $filename = pathinfo($file, PATHINFO_BASENAME);
                $zip->addFile($file, "data/" . $filename);
            }
        }

        $this->info("✅DB Data Dump Done");

        $zip->close();

        $files = FileUtility::getFileList($data_path);

        $this->info("Deleting Files");

        foreach ($files as $file) {
            if (!is_dir($file)) {
                $table = pathinfo($file, PATHINFO_FILENAME);
                if (!isset($table_sql_list[$table])) {
                    $this->info($table);
                    unlink($file);
                }
            }
        }

        $this->info("All Files Located at $path");
    }

    public static function getStorageDisk()
    {
        $disk = Storage::disk(DEFAULT_DISK);

        return $disk;
    }
}
