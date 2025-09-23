<?php

namespace App\Http\Controllers\Backend;

<<<<<<< HEAD
use App\Helpers\Menu;
use App\Http\Controllers\WebController;
use App\Models\BaseModel;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
=======
use App\Exceptions\UserException;
use App\Helpers\Menu;
use App\Http\Controllers\WebController;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
>>>>>>> base_v3

class BackendController extends WebController
{
    protected function beforeViewRender()
    {
<<<<<<< HEAD
        if (!parent::beforeViewRender())
        {
=======
        if (!parent::beforeViewRender()) {
>>>>>>> base_v3
            return false;
        }

        $request = request();

<<<<<<< HEAD
        if ( $request->ajax() ) {
            $this->layout = "backend.layouts.ajax";
        }
        else {
            $this->layout = "backend.layouts.main";
        }
        
=======
        if ($request->ajax()) {
            $this->layout = "backend.layouts.ajax";
        } else {
            $this->layout = "backend.layouts.main";
        }

>>>>>>> base_v3
        $this->viewPrefix = "backend." . $this->getControllerName();

        $request = request();

        $current_route_name = Route::currentRouteName();

<<<<<<< HEAD
        if ($current_route_name)
        {
            Menu::setCurrentRouteName($current_route_name);
        }

        $auth_user = Auth::user();

        $menus = $header_menu_list = [];
        if ($auth_user)
        {
=======
        if ($current_route_name) {
            Menu::setCurrentRouteName($current_route_name);
        }

        $current_url = request()->getRequestUri();

        $auth_user = Auth::user();

        $menus = $header_menu_list = [];
        if ($auth_user) {
>>>>>>> base_v3
            $menus = Menu::get($auth_user->id);
        }

        // d($menus, true);

<<<<<<< HEAD
        $header_menu_list = Menu::getList($menus);
        $breadcums = Menu::getBreadcums($menus);

        $userListCache = User::getListCache();

        $partial_path = "backend.partials";

        $this->setForView(compact("current_route_name", "menus", "header_menu_list", "partial_path", "breadcums", "userListCache"));
=======
        $header_menus = Menu::getList($menus);
        $header_menu_list = [];

        foreach($header_menus as $header_menu)
        {
            if ($header_menu['url'] && $header_menu['url'] != "#")
            {
                $header_menu_list[$header_menu['url']] = $header_menu['title'];
            }
        }

        $breadcums = Menu::getBreadcums($menus);

        $auth_user = Auth::user();

        $unread_notifications = $auth_user->unreadNotifications()->limit(5)->get();

        foreach($unread_notifications as $unread_notification)
        {
            $unread_notification->afterFind();
        }

        $unread_notification_count = $auth_user->unreadNotifications()->count();

        $userListCache = User::getListCache("id", "display_name", "person_name");

        $partial_path = "backend.partials";

        $screen_name = "";
        if (strpos($current_route_name, ".create") !== false) {
            $screen_name = "add";
        } else if (strpos($current_route_name, ".index") !== false) {
            $screen_name = "summary";
        } else if (strpos($current_route_name, ".edit") !== false) {
            $screen_name = "edit";
        }

        $index_table_info_options = [
            "show_as" => "celec_datetime"
        ];

        $false = false;
        $empty_list = [];

        $this->setForView(compact(
            "current_route_name", "current_url", "menus", "header_menu_list", "partial_path", "breadcums", "userListCache", "auth_user", "screen_name", 
            "index_table_info_options", "unread_notifications", "unread_notification_count", "false", "empty_list"
        ));
>>>>>>> base_v3

        return true;
    }

    public function destroy($id)
    {
        $request = request();

<<<<<<< HEAD
        try
        {       
            $model = $this->modelClass::findOrFail($id); 

            if (!$this->delete($model))
            {
                throw new Exception("Fail to Delete");
            }

            $this->saveSqlLog();

            return $this->afterDestroy($model);
        }
        catch(\Exception $ex)
        {
            if ( $request->ajax() ) {
                return $this->responseJson(['status'=> 0, 'msg'=> $ex->getMessage()]);
=======
        try {
            $model = $this->modelClass::findOrFail($id);

            $this->beforeDelete($model);

            if (!$this->delete($model)) {
                throw new Exception("Fail to Delete");
            }

            return $this->afterDestroy($model);
        } catch (\Exception $ex) {
            if ($request->ajax()) {
                return $this->responseJson(['status' => 0, 'msg' => $ex->getMessage()]);
>>>>>>> base_v3
            }

            $this->saveSqlLog();

            return back()->with('fail', $ex->getMessage());
        }
    }

    public function activate($id)
    {
        $response = ["status" => 1];
<<<<<<< HEAD
        try
        {
=======
        try {
>>>>>>> base_v3
            $model = $this->modelClass::findOrFail($id);

            $model->activate();

            $response['url'] = route($this->routePrefix . ".de_activate", ['id' => $id]);
<<<<<<< HEAD
        }
        catch(Exception $ex)
        {
=======
        } catch (Exception $ex) {
>>>>>>> base_v3
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }

    public function de_activate($id)
    {
        $response = ["status" => 1];
<<<<<<< HEAD
        try
        {
=======
        try {
>>>>>>> base_v3
            $model = $this->modelClass::findOrFail($id);

            $model->deActivate();

            $response['url'] = route($this->routePrefix . ".activate", ['id' => $id]);
<<<<<<< HEAD
        }
        catch(Exception $ex)
        {
=======
        } catch (Exception $ex) {
>>>>>>> base_v3
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }

    public function ajax_get($id)
    {
        $response = ["status" => 1];
<<<<<<< HEAD
        try
        {
            $model = $this->modelClass::findOrFail($id);

            $response['data'] = $model->toArray();
        }
        catch(Exception $ex)
        {
=======
        try {
            $model = $this->modelClass::findOrFail($id);

            $response['data'] = $model->toArray();
        } catch (Exception $ex) {
>>>>>>> base_v3
            $response['status'] = 0;
            $response['msg'] = $ex->getMessage();
        }

        return $this->responseJson($response);
    }
<<<<<<< HEAD
=======

    public function ajax_update_field(Request $request)
    {
        try {

            $validatedData = $request->validate([
                "pk" => "required",
                "name" => "required",
                "value" => "required",
            ]);

            $record = $this->modelClass::where("id", $validatedData['pk'])->first();

            if (!$record)
            {
                throw new Exception("Wrong ID : " . $validatedData['pk']);
            }

            $record->{$validatedData['name']} = $validatedData['value'];

            if (!$record->save())
            {
                throw new UserException("Fail To Save", UserException::ERROR_LARAVEL_FAIL);
            }

            $response = $validatedData;

            return $this->responseJson($response);
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
>>>>>>> base_v3
}
