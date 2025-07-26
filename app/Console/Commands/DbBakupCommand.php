<?php

namespace App\Console\Commands;

use App\Exceptions\UserException;
use App\Helpers\DateUtility;
use App\Helpers\FileUtility;
use App\Helpers\MyCommand;
use App\Models\BaseMongoModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DbBakupCommand extends MyCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {connection?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup For Mysql And Mongodb';

    public static string $path = "files/backup";

    public FileUtility $fileUtility;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = $this->argument('connection');

        $this->logCreate([
            "command" => $this->signature,
            "description" => $this->description
        ]);

        $db_config_list = config("database.connections");

        $options = $flip_options = [];

        foreach ($db_config_list as $key => $arr) {
            $name =  $arr['database'] . " (" . $key . ")";
            $options[] = $name;
            $flip_options[$name] = $key;
        }

        if (!$options) {
            $this->error("No Database Config found");
            return;
        }

        if ($connection)
        {
            if (!isset($db_config_list[$connection]))
            {
                $this->error("Connection not found : $connection in database.connections");
                unset($connection);
            }
        }
        
        if (isset($connection) && $connection)
        {
            $db_config_key = $connection;
            $db_config = $db_config_list[$db_config_key];
        }
        else
        {
            $choice = $this->choice(
                'Select Database :',
                $options,
            );

            if (!$choice) {
                return;
            }

            $db_config_key = $flip_options[$choice];
            $db_config = $db_config_list[$flip_options[$choice]];
        }

        $this->fileUtility = new FileUtility(self::getStorageDisk());

        if ($db_config['driver'] == "mysql") {
            $this->backup_mysql($db_config_key, $db_config);
            $this->delete_old_backup($db_config_key, $db_config);
        }

        if ($db_config['driver'] == "mongodb") {
            $this->backup_mongodb($db_config_key, $db_config);
            $this->delete_old_backup($db_config_key, $db_config);
        }

        $this->logUpdate([]);

        return true;
    }

    public function backup_mysql($config_key, $db_config)
    {
        $this->info('Mysql Backup Started');

        $sub_path = date("d-M-Y");

        $path = $this->fileUtility->getDisk()->path(self::$path . "/" . $config_key . "/" . $sub_path) . "/";

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
            $this->error("Command is not working");
            $this->info($command);
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

        $ignore_tables = [
            "telescope_entries",
            "telescope_entries_tags",
            "telescope_monitoring",
        ];

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
                $this->error("Command is not working");
                $this->info($command);
            } else {
                $this->line($table);
                $filename = pathinfo($file, PATHINFO_BASENAME);
                $zip->addFile($file, "data/" . $filename);
            }
        }

        $this->info("✅Mysql Data Dump Done");

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

    public function delete_old_backup($config_key, $db_config)
    {
        $last_date_time = DateUtility::change(null, -15, DateUtility::DAYS, DateUtility::DATE_FORMAT);

        $this->info('Deleting Old Backup older than ' . $last_date_time);

        $counters = [
            "files" => [
                "found" => 0,
                "delete" => 0,
            ],
            "folders" => [
                "found" => 0,
                "delete" => 0,
            ],
        ];

        $path = $this->fileUtility->getDisk()->path(self::$path . "/" . $config_key);

        $files_folders = FileUtility::getFilesAndFolderOlderthan($path, $last_date_time, [], true);
        
        // dd($files_folders);

        $counters['files']['found'] = count($files_folders['files']);
        $counters['folders']['found'] = count($files_folders['folders']);

        foreach($files_folders['files'] as $file)
        {
            if (unlink($file)) 
            {
                $counters['files']['delete']++;
            }
            else
            {
                throw new UserException("Fail to Delete File : $file", UserException::ERROR_PHP_FAIL);
            }
        }

        foreach($files_folders['folders'] as $folder)
        {
            $files = FileUtility::getFileList($folder, [], true);
            if (!$files)
            {
                if (rmdir($folder)) 
                {
                    $counters['folders']['delete']++;
                }
            }
        }

        foreach($counters as $label => $arr)
        {
            $arr = array_human_keys($arr);

            $list = [];

            foreach($arr as $k => $v)
            {
                $list[] = $k . ":" . $v;
            }

            $this->info($label . " : " . implode(", ", $list));
        }
    }

    public function backup_mongodb($config_key, $db_config)
    {
        $this->info('Mongodb Backup Started');

        $sub_path = date("d-M-Y");

        $path = $this->fileUtility->getDisk()->path(self::$path . "/" . $config_key . "/" . $sub_path) . "/";

        FileUtility::createFolder($path);

        $db_info = BaseMongoModel::getCollections();

        $db_name = $db_info['database'];

        foreach($db_info['collections'] as $collection_name)
        {
            $file = $path . $collection_name . ".json";

            $command = "mongoexport --uri " . $db_config['dsn'] . " --db $db_name --collection $collection_name --out $file";

            exec($command, $output, $return_var);

            if ($return_var == 2) {
                $this->error("Command is not working");
                $this->info($command);
            } else {
                $this->line($collection_name);
            }
        }

        $this->info("✅Mongodb Data Dump Done");

        $this->info("All Files Located at $path");
    }

    public static function getStorageDisk()
    {
        $disk = Storage::disk(DEFAULT_DISK);

        return $disk;
    }
}
