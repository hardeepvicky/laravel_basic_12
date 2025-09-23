<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\LaravelExtend;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\AppCategory;
use App\Models\AppCity;
use App\Models\AppCountry;
use App\Models\AppLead;
use App\Models\AppLeadType;
use App\Models\AppSource;
use App\Models\AppState;
use App\Models\AppStatus;
use App\Models\Enquiry;
use App\Rules\IndianMobileNumber;
use Exception;
use Illuminate\Support\Facades\DB;

class EnquiryController extends FrontendController
{
    protected String $routePrefix = "enquiry";

    protected $modelClass = Enquiry::class;

    public function index()
    {
        $country_list = AppCountry::getListCache();

        $source_list = AppSource::getList();

        $status_list = AppStatus::getList();

        $type_list = AppLeadType::getList();

        $state = AppState::where("name", "Uttar Pradesh")->first();

        $city = null;

        if ($state) {
            $city = AppCity::where("name", "Kasganj")->where("state_id", $state->id)->first();
        }

        $record = [
            "country_id" => array_search("India +91", $country_list),
            "state_id" => $state ? $state->id : "",
            "city_id" => $city ? $city->id : "",
            "source_id" => array_search("UPSE26", $source_list),
            "status_id" => array_search("Hot", $status_list),
            "type_id" => array_search("Sales", $type_list),
        ];

        $category_list = AppCategory::getList();

        $this->setForView(compact("country_list", "category_list", "source_list", "type_list", "status_list", "record"));

        return $this->view("index");
    }

    private function _common_validation_rules()
    {
        return [
            'country_id' => ["required"],
            'state_id' => ["required"],
            'city_id' => ["required"],
            'name' => ["required"],
            'mobile' => ["required", new IndianMobileNumber()],
            'email' => ["nullable", "email"],
            'address' => "",
            'message' => "",
            'source_id' => "required",
            'type_id' => "required",
            'status_id' => "required",
            'category_id' => "",
            "file" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240"
        ];
    }

    private function _common_validation_messages()
    {
        return [
            "country_id.required" => "Country is required",
            "state_id.required" => "State is required",
            "city_id.required" => "City is required",
            "source_id.required" => "Source is required",
            "type_id.required" => "Type is required",
            "status_id.required" => "Status is required",
            "category_id.required" => "Product Category is required",
        ];
    }

    public function store(Request $request)
    {
        die("Code not impelement yet");
        $this->beforeCreate();

        $rules = $this->_common_validation_rules();

        $model = new $this->modelClass();

        $unique_validation_rules = $model->getUniqueValidationRules();

        $rules = array_merge_recursive($rules, $unique_validation_rules);

        // dd($rules);

        $messages = $this->_common_validation_messages();

        $messages = array_merge($messages, []);

        $validatedData = $request->validate($rules, $messages);

        // dd($validatedData);

        DB::beginTransaction();

        try {

            LaravelExtend::checkDatabaseConnection("mysql_erp_old", true);

            $lead_data = $validatedData;

            $lead_data['phone'] = $lead_data['mobile'];
            unset($lead_data['mobile']);

            $lead_data['state'] = $lead_data['state_id'];
            unset($lead_data['state_id']);

            $lead_data['district'] = $lead_data['city_id'];
            unset($lead_data['city_id']);

            $lead_data['product_id'] = $lead_data['city_id'];
            unset($lead_data['city_id']);

            $lead_data['district'] = $lead_data['city_id'];
            unset($lead_data['city_id']);

            $lead_data['district'] = $lead_data['city_id'];
            unset($lead_data['city_id']);

            if ($request->hasFile("file")) {
                // d(get_class_info($request->file), true);
                $file = $request->file("file");
                $fileName = 'image_' . md5(uniqid()) . '.' . $file->getClientOriginalExtension(); // Another way to generate unique name

                $file_path = $file->storeAs($model->getFilePath(), $fileName, DEFAULT_DISK);

                if (!$file_path) {
                    throw new Exception("file not saved");
                }

                $validatedData['file'] = $file_path;
            } else {
                $validatedData['file'] = null;
            }

            $model = $this->modelClass::create($validatedData);





            DB::commit();

            $this->saveSqlLog();

            return $this->afterSave($model);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->saveSqlLog();

            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }

    public function ajax_get_state_list($country_id)
    {
        $response = ["status" => 1];

        $response['data'] = AppState::where("country_id", $country_id)->select("id", "name")->get()->toArray();

        return $this->responseJson($response);
    }

    public function ajax_get_city_list($state_id)
    {
        $response = ["status" => 1];

        $response['data'] = AppCity::where("state_id", $state_id)->select("id", "name")->get()->toArray();

        return $this->responseJson($response);
    }
}
