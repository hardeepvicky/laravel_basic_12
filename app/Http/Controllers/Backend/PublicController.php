<?php

namespace App\Http\Controllers\Backend;

<<<<<<< HEAD
use Exception;
use App\Helpers\FileUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
=======
use App\Exceptions\UserException;
use Exception;
use App\Helpers\FileUtility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
>>>>>>> base_v3

class PublicController extends BackendController
{
    public function ajax_upload(Request $request)
    {
        die("code not implemented");
        dd($request->get("file"));
    }

    public function ajax_upload_base64(Request $request)
    {
        $response = ["status" => 1, "msg" => ""];

<<<<<<< HEAD
        try
        {
            $base64 = $request->get("base64", null);
            $filename = $request->get("filename", null);

            if (!$base64)
            {
                throw_exception("base64 not found in request");
            }

            if (!$filename)
            {
=======
        try {
            $base64 = $request->get("base64", null);
            $filename = $request->get("filename", null);

            if (!$base64) {
                throw_exception("base64 not found in request");
            }

            if (!$filename) {
>>>>>>> base_v3
                throw_exception("filename not found in request");
            }

            $path = Config::get('constant.path.temp');

            $response['file'] = FileUtility::base64ToFile($base64, $path, $filename);
            $response['filename'] = pathinfo($response['file'], PATHINFO_BASENAME);
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
<<<<<<< HEAD
}
=======

    public function get_file_url(Request $request)
    {
        $file_path = $request->query("file_path");

        $file_path = urldecode($file_path);

        if ($file_path) {
            if (Storage::disk(DEFAULT_DISK)->fileExists($file_path)) {
                $file_path = Storage::disk(DEFAULT_DISK)->path($file_path);
            } else if (file_exists($file_path)) {
            } else {
                $file_path = NOT_FOUND_IMAGE_PATH;
            }
        } else {
            $file_path = NOT_FOUND_IMAGE_PATH;
        }

        $ext = pathinfo($file_path, PATHINFO_EXTENSION);

        download_start($file_path, get_mime_type_from_extension($ext));
    }
}
>>>>>>> base_v3
