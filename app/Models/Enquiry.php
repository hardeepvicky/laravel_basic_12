<?php

namespace App\Models;

use App\Helpers\FileUtility;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpParser\Node\Stmt\Else_;

class Enquiry extends BaseMongoModel
{
    protected $guarded = ["id"];

    public function getUniqueValidationRules()
    {
        return [
            "email" => [Rule::unique($this->connection . "." . $this->getTable(), "email")],
            "mobile" => [Rule::unique($this->connection . "." . $this->getTable(), "mobile")],
        ];
    }

    protected static function booted()
    {
        // Register a listener for the 'retrieved' event
        static::retrieved(function (Enquiry $model) {

            $model->file_url = "";
            if (isset($model->file) && $model->file) {
                if (Storage::disk(DEFAULT_DISK)->fileExists($model->file)) {
                    $model->file_url = route("get_file_url", ["file_path" => urlencode($model->file)]);
                } else {
                    $model->file_url = url(NOT_FOUND_IMAGE_PATH);
                }
            }
        });
    }
}
