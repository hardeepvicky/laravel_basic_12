<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\UserException;
use App\Helpers\LaravelExtend;
use App\Http\Controllers\WebController;
use App\Models\Country;
use App\Models\FieldSetting;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Type;
use App\Models\User;
use App\Rules\MobileNumber;
use App\Rules\ReCaptcha;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RegisterController extends WebController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'type' => ['required', 'string', 'max:255'],
            'mobile' => ['required', new MobileNumber],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            "g-recaptcha-response" => ['required', new ReCaptcha]
        ];

        $user = new User();
        $rules = LaravelExtend::merge_validation_rules($rules, $user->getRequiredIfRules(request()));
        $rules = LaravelExtend::merge_validation_rules($rules, $user->getUniqueValidationRules(request()));

        return Validator::make($data, $rules,
        [
            "g-recaptcha-response.required" => "Google Capcha verification required"
        ]
        );
    }

    public function signup_form()
    {
        $model = new User();

        $this->setForView(compact("model"));

        return $this->view(__FUNCTION__);
    }

    public function register()
    {
        $request = request();

        $data = $request->all();

        $validateData = $this->validator($data)->validate();

        DB::beginTransaction();

        try
        {
            $role = Role::where("code", "external_user")->first();

            if (!$role) {
                throw new UserException("Role Not Found: external_user");
            }
            
            $user = new User();
            
            $validateData['name'] = $user->getNextId();

            $validateData['password'] = Hash::make($validateData['password']);

            $user = User::create($validateData);

            $user->assignRoles([$role->id]);

            $url = url()->query(config("url") . "/otp_verify/" . $user->uid, ["otp_sent" => true]);

            DB::commit();
            
            return redirect($url);
        }
        catch(Exception $ex)
        {
            DB::rollBack();

            throw $ex;

            return redirect()->back()->with('fail', $ex->getMessage());
        }
    }


    public function forgot_password(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $validatedData = $request->validate([
                    'email' => 'required:email',
                ]);

                $user = User::where("email", $validatedData['email'])->first();

                if (!$user) {
                    throw new \Exception("This Email is not registered with us");
                }

                if ($user->dont_send_email) {
                    throw new \Exception("We can't send email. Beacuase Don't send email is enabled");
                }

                LaravelExtend::send_forgot_password_link($user);

                return back()->with('success', 'Email Sent Successfully');
            } catch (\Exception $ex) {
                return back()->with('fail', $ex->getMessage())->withInput();
            }
        }

        $company_name = Setting::getValue("company_name");

        return view("auth.forgot_password", [
            "layout" => "auth.layouts.login",
            "company_name" => $company_name,
        ]);
    }

    public function create_password(Request $request, $uid)
    {
        $user = User::where("uid", $uid)->first();

        if (!$user) {
            die("Invalid UID");
        }

        if ($request->isMethod('post')) {
            try {
                $validatedData = $request->validate([
                    'password' => 'required|min:5|confirmed',
                ]);

                $user->password = bcrypt($validatedData['password']);

                if (!$user->save()) {
                    throw_exception("Fail To Save User");
                }

                return redirect()->route("login")->with('success', 'Password Changed Successfully');
            } catch (\Exception $ex) {
                return back()->with('fail', $ex->getMessage())->withInput();
            }
        }

        $company_name = Setting::getValue("company_name");

        return view("auth.create_password", [
            "layout" => "auth.layouts.login",
            "company_name" => $company_name,
        ]);
    }

    
    public function save_data_before_signup(Request $request)
    {
        $response = ["status" => 1];

        $data = $request->only([
            "sub_type", "country_id"
        ]);

        Session::put("save_data_before_signup", $data);
        Session::put("user_timezone", $request->get("timezone"));
        
        return $this->responseJson($response);
    }

    public function save_data_before_signin(Request $request)
    {
        $response = ["status" => 1];

        Session::put("user_timezone", $request->get("timezone"));
        
        return $this->responseJson($response);
    }

    protected function beforeViewRender()
    {
        if (!parent::beforeViewRender()) {
            return false;
        }

        $this->routePrefix = "";

        $this->viewPrefix = "auth";

        $this->layout = "auth.layouts.login";
    }
}
