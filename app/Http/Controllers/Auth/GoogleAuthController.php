<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\UserException;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Exception;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google’s OAuth page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback()
    {
        try {
            // Get the user information from Google
            $user = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            return redirect('/')->with('error', 'Google authentication failed.');
        }


        DB::beginTransaction();

        try
        {
            $db_user = User::where("email", $user->getEmail())->first();

            $data = [
                "person_name" => $user->getName(),
                "email" => $user->getEmail(),
                "avatar" => $user->getAvatar(),
                "google_id" => $user->id,
                "created_via" => User::CREATED_VIA_GOOGLE
            ];

            $data['person_last_name'] = "";

            if (strpos($data['person_name'], " ") !== false)
            {
                $arr = explode(" ", $data['person_name']);

                $data['person_name'] = $arr[0];

                unset($arr[0]);

                $data['person_last_name'] = implode(" ", $arr);
            }

            $data_before_signup = session("save_data_before_signup");
            
            if (!$db_user && !$data_before_signup)
            {
                abort("Email " . $user->getEmail() . " is not registed with us", ACTION_NOT_PROCEED);
            }

            if (is_array($data_before_signup) && $data_before_signup)
            {
                $data = array_merge($data, $data_before_signup);
            }
            
            $role = Role::where("code", "external_user")->first();

            if (!$role) {
                throw new UserException("Role Not Found: external_user");
            }
            
            $user = new User();
            
            if (!$db_user)
            {
                $data['type'] = User::TYPE_EXTERNAL;
                $data['name'] = $user->getNextId();
                $data['password'] = "";
            }

            $user = User::insertOrUpdate($data);

            $user->is_active = true;
            $user->login_timezone = session("user_timezone");
            $user->save();

            $user->assignRoles([$role->id]);
            
            DB::commit();
            
            Auth::login($user);

            return redirect("/home");
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            throw $ex;
        }
    }
}
