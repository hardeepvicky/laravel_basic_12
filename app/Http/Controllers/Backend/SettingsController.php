<?php

namespace App\Http\Controllers\Backend;

use App\Models\NotificationSetting;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class SettingsController extends BackendController
{
    public String $routePrefix = "admin.setting";

    protected $modelClass = Setting::class;

    public function general(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            //d($data["data"]['text']); exit;
            foreach ($data["data"]['text'] as $key => $value) {

                if (is_null($value) || strlen(trim($value)) == 0) {
                    $value = "0";
                }

                $settingModel = Setting::insertOrUpdate([
                    "name" => $key,
                    "value" => $value,
                ]);
            }

            Session::flash('success', 'Settings has been saved');
        }

        $form = [
            'url' => route($this->routePrefix . '.general'),
            'method' => 'POST',
        ];

        $record_list = Setting::pluck("value", "name")->toArray();

        //d($record_list); exit;

        $this->setForView(compact('form', 'record_list'));

        return $this->view(__FUNCTION__);
    }

    public function cache_clear()
    {
        Artisan::call('cache:clear');

        return "Cache Cleared";
    }
}
