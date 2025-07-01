<?php

namespace App\Exceptions;

use App\Helpers\Util;
use Exception;

class ApiException extends Exception
{
    public const ERROR_GENERAL = 512;

    public $api_error_code, $api_error_msg;

    public $data;
    public function __construct(String $message, int $code = 512, $data = [])
    {
        parent::__construct($message, $code);
        $this->setData($data);
    }

    public function setData($data)
    {
        $this->data = $data;

        $data = Util::applyAllforKey($data, ["trim", "strtolower"]);

        if (isset($data['errorcode'])) {
            $this->api_error_code = $data['errorcode'];
        }

        if (isset($data['errormessage'])) {
            $this->api_error_msg = $data['errormessage'];
        }
    }
}
