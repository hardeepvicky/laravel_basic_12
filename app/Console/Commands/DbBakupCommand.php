<?php

namespace App\Console\Commands;

use App\Exceptions\UserException;
use App\Helpers\FileUtility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DbBakupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {connection? : The name of connection of laravel database config}';

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

        $this->info('Database Backup Started');

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
        

        if ($connection)
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
        }

        return true;
    }

    public function backup_mysql($config_key, $db_config)
    {
        $this->info('Database Backup Started');

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
