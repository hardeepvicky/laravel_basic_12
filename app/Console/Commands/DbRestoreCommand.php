<?php

namespace App\Console\Commands;

use App\Exceptions\UserException;
use App\Helpers\FileUtility;
use App\Helpers\LaravelExtend;
use App\Helpers\MyCommand;
use App\Models\BaseMongoModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DbRestoreCommand extends MyCommand
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
        if (!App::isProduction()) 
        {
            $this->info("App is in production, restore can be run in debug off mode");
            return;
        }

        $this->logCreate([
            "command" => $this->signature,
            "description" => $this->description
        ]);

        $db_config_list = config("database.connections");

        $options = $flip_options = [];

        foreach ($db_config_list as $key => $arr) {
            $name =  $key . " (" . $arr['driver'] . ")";
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

        $db_config_key = $flip_options[$choice];

        $db_config = $db_config_list[$flip_options[$choice]];

        $this->fileUtility = new FileUtility(self::getStorageDisk());

        if ($db_config['driver'] == "mysql") {
            $this->restore_mysql($db_config_key, $db_config);
        }
        else if ($db_config['driver'] == "mongodb") {
            $this->restore_mongodb($db_config_key, $db_config);
        }

        $this->logUpdate([]);

        return true;
    }

    public function restore_mysql($config_key, $db_config)
    {
        $this->info('Mysql restore Started');

        $path = $this->fileUtility->getDisk()->path(self::$path . "/" . $config_key);

        if (!file_exists($path))
        {
            $this->info("there is no any folder at " . self::$path);
            return;
        }

        $folder_list = FileUtility::getFolderList($path);

        if (empty($folder_list)) {
            $this->info("there is no any folder at $path");
            return;
        }

        $choice = $this->choice(
            'Select Folder :',
            $folder_list,
        );

        if (!$choice) {
            return;
        }

        $path = $path . "/" . $choice;

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

            if (LaravelExtend::checkDatabaseExist($config_key))
            {
                $user_input = $this->ask('Database already exist, would you like to drop database [Y/N] :');

                $user_input = strtoupper(trim($user_input));

                if ($user_input == "Y")
                {
                    $this->info("Droping database...");
                    DB::statement("DROP DATABASE $db_name");
                }
            }
            
            if (!LaravelExtend::checkDatabaseExist($config_key))
            {
                $this->info("Creating database...");
                DB::statement("CREATE DATABASE $db_name");
            }

            foreach ($files as $k => $file) {
                if (strpos($file, "_st.sql") !== false) {
                    
                    $this->info("Importing Structure..");

                    $command = 'mysql -P' . $db_config['port'] . ' -u' . $db_config['username'] . ' -p' . $db_config['password'] . ' ' . $db_name . ' < "' . $file . '"';

                    exec($command, $output, $return_var);

                    if ($return_var == 2) {
                        $this->error("Command is not working");
                        $this->info($command);
                        return;
                    }

                    unset($files[$k]);
                }
            }
        }

        $this->info("Importing Data..");

        if ($choice == "Full Restore" || $choice == "Data Restore Only") {

            foreach ($files as $k => $file)
            {
                if (strpos($file, "_st.sql") === false) 
                {
                    $file_name = pathinfo($file, PATHINFO_FILENAME);

                    $this->line($file_name);

                    $command = 'mysql -P' . $db_config['port'] . ' -u' . $db_config['username'] . ' -p' . $db_config['password'] . ' ' . $db_name . ' < "' . $file . '"';

                    exec($command, $output, $return_var);

                    if ($return_var == 2) {
                        $this->error("Command is not working");
                        $this->info($command);
                    }
                }
            }
        }

        $this->info("✅Mysql Import Done");
    }

    public function restore_mongodb($config_key, $db_config)
    {
        $this->info('Mongodb Backup Started');

        $path = $this->fileUtility->getDisk()->path(self::$path . "/" . $config_key);

        if (!file_exists($path))
        {
            $this->info("there is no any folder at " . self::$path);
            return;
        }

        $folder_list = FileUtility::getFolderList($path);

        if (empty($folder_list)) {
            $this->info("there is no any folder at $path");
            return;
        }

        $choice = $this->choice(
            'Select Folder :',
            $folder_list,
        );

        if (!$choice) {
            return;
        }

        $path = $path . "/" . $choice;

        $files = FileUtility::getFileList($path, ["json"], true);

        if (empty($files)) {
            $this->info("No JSON file found");
            return;
        }

        $options = ["Full Restore", "Data Restore Only"];

        $choice = $this->choice(
            'Select :',
            $options,
        );

        if (!$choice) {
            return;
        }

        $db_info = BaseMongoModel::getCollections($config_key);

        $db_name = $db_config['database'];

        if ($choice == "Full Restore") 
        {
            if ($db_info)
            {
                $user_input = $this->ask('Database already exist, would you like to drop database [Y/N] :');

                $user_input = strtoupper(trim($user_input));

                if ($user_input == "Y")
                {
                    $this->info("Droping database...");
                    $result = DB::connection($config_key)->command(['dropDatabase' => 1]);
                    
                    if (isset($result->ok) && $result->ok == 1)
                    {

                    }
                    else
                    {
                        $this->error("fail to drop database $db_name");
                        return;
                    }
                }
            }
        }

        $this->info("Importing Data..");

        foreach ($files as $k => $file)
        {
            $file_name = pathinfo($file, PATHINFO_FILENAME);

            $this->line($file_name);

            $command = "mongoimport --uri " . $db_config['dsn'] . " --db $db_name --collection $file_name --drop --file $file";

            // d($command);

            exec($command, $output, $return_var);

            if ($return_var == 2) {
                $this->error("Command is not working");
                $this->info($command);
            }
        }

        $this->info("✅Mongodb Import Done");
    }

    public static function getStorageDisk()
    {
        $disk = Storage::disk(DEFAULT_DISK);

        return $disk;
    }
}
