<?php

namespace App\Console\Commands;

use App\Exceptions\UserException;
use App\Helpers\MyCommand;
use App\Models\User;

class SetPasswordCommand extends MyCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logModel = $this->logCreate([
            "command" => $this->signature,
            "description" => $this->description
        ]);

        $id_or_email = $this->ask('Enter User Id or Email');

        $id_or_email = trim($id_or_email);

        if (!$id_or_email)
        {
            return;
        }

        $field = filter_var($id_or_email, FILTER_VALIDATE_EMAIL) ? 'email' : 'id';

        $users = User::where($field, $id_or_email)->get();

        if ($users->count() == 0)
        {
            $this->error("No Record with $id_or_email");
            return;
        }

        if ($users->count() > 1)
        {
            $this->error("Multiple records with $id_or_email");
        }

        $user = $users[0];

        $password = $this->ask('Enter Password');

        $password = trim($password);

        if (!$password)
        {
            $this->error("Password is empty");
            return;
        }

        $user->setPassword($password);

        if ($user->save())
        {
            $this->info("✅ Password updated");
        }
        else
        {
            throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
        }

        $logModel = $this->logUpdate([
            "password changed of user : " . $user->getDisplayName()
        ]);
    }
}
