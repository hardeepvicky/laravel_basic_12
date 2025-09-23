<?php

namespace App\Http\Controllers\Auth;

<<<<<<< HEAD
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebController;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
=======
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
>>>>>>> base_v3
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
<<<<<<< HEAD
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
=======
>>>>>>> base_v3
     * Create a new controller instance.
     *
     * @return void
     */
<<<<<<< HEAD
    public function __construct()
    {
        $this->middleware('guest');

        $this->routePrefix = "";
        
        $this->viewPrefix = "auth";

        $this->layout = "backend.layouts.login";
    }

    public function showRegistrationForm()
    {
        return $this->view("register");
    }
=======
    public function __construct() {}
>>>>>>> base_v3

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
<<<<<<< HEAD
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'profile_image' => '',
            'password' => Hash::make($data['password']),
        ]);
    }
=======
        $rules = [
            'type' => ['required', 'string', 'max:255'],
            'sub_type' => ['required', 'string', 'max:255'],
            'country_id' => ['required'],
            'person_name' => ['required', 'string', 'max:255'],
            'person_last_name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', new MobileNumber],
            'whatsapp_mobile' => ['required', new MobileNumber],
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
        $type_conditions = [
            "type" => Type::getType(new User(), "sub_type_for_signup_form")
        ];

        $sub_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $country_list = Country::getListCache();

        $my_country = Country::getMyCountry();

        if (!$my_country)
        {
            throw new UserException("Country India not found");
        }

        $model = new User();
        if ($my_country)
        {
            $model->country_id = $my_country->id;
        }

        $field_labels = FieldSetting::getDynamicFields(User::class);

        $this->setForView(compact("sub_type_list", "country_list", "my_country", "model", "field_labels"));

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

            $validateData["created_via"] = User::CREATED_VIA_REGISTRATION;

            $user = User::create($validateData);

            $user->assignRoles([$role->id]);

            $url = url()->query(config('app.url') . "/otp_verify/" . $user->uid, ["otp_sent" => true]);

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
>>>>>>> base_v3
}
