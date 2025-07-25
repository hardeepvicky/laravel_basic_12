<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LaravelExtend;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebController;
use App\Models\Country;
use App\Models\FieldSetting;
use App\Models\Setting;
use App\Models\Type;
use App\Models\User;
use App\Rules\MobileNumber;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
        return Validator::make($data, [
            'type' => ['required', 'string', 'max:255'],
            'sub_type' => ['required', 'string', 'max:255'],
            'country_id' => ['required'],
            'person_name' => ['required', 'string', 'max:255'],
            'person_last_name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', new MobileNumber],
            'whatsapp_mobile' => ['required', new MobileNumber],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    public function signup_form()
    {
        $type_conditions = [
            "type" => Type::getType(new User(), "sub_type_for_signup_form")
        ];

        $sub_type_list = Type::getList("code", "display_name", $type_conditions, "list_display_order");

        $country_list = Country::getListCache();

        $my_country = Country::getMyCountry();

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

        // $recaptcha_response = $request->input('g-recaptcha-response');

        // if (is_null($recaptcha_response)) {
        //     return redirect()->back()->with('fail', 'Please Complete the Recaptcha to proceed');
        // }

        // $url = "https://www.google.com/recaptcha/api/siteverify";

        // $body = [
        //     'secret' => config('services.recaptcha.secret'),
        //     'response' => $recaptcha_response,
        //     'remoteip' => $request->ip() //anonymize the ip to be GDPR compliant. Otherwise just pass the default ip address
        // ];

        // $response = Http::asForm()->post($url, $body);

        // $result = json_decode($response);

        // if ($response->successful() && $result->success == true) {

            $data = request()->all();

            $validateData = $this->validator($data)->validate();

            DB::beginTransaction();

            try
            {
                $user = new User();
                
                $validateData['name'] = $user->getNextId();

                $validateData['password'] = Hash::make($validateData['password']);

                $user = User::create($validateData);

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


        // } else {
        //     return redirect()->back()->with('fail', 'Please Complete the Recaptcha Again to proceed');
        // }

        // return null;
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
