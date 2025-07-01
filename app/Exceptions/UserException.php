<?php

namespace App\Exceptions;

use Exception;

class UserException extends Exception
{
    public const ERROR_GENERAL = 512;
    public const ERROR_LARAVEL_FAIL = 513;
    public const ERROR_PHP_FAIL = 514;
    public const ERROR_FROM_VALIDATION = 515;
    public const ERROR_USER_ACTION_RESTRICT = 516;
    public const ERROR_USER_DATA_WRONG = 517;

    public $data;
    public function __construct(String $message, int $code = 512, $data = [])
    {
        parent::__construct($message, $code);
        $this->setData($data);
    }

    public function setData($data)
    {
        $data['error_title'] = "Unknown Error";
        $data['file_name'] = $this->getFile();
        $data['file_line_no'] = $this->getLine();

        if (!isset($data['html']))
        {
            $data['html'] = "";
        }

        switch ($this->code) {
            case self::ERROR_GENERAL:
                $data['error_title'] = "Error bcoz of Developer's mistake";
                break;

            case self::ERROR_LARAVEL_FAIL:                
                $data['error_title'] = "Error bcoz of Laravel internal failure";
                break;

            case self::ERROR_PHP_FAIL:
                $data['error_title'] = "Error bcoz of PHP internal failure";
                break;

            case self::ERROR_USER_ACTION_RESTRICT:
                $data['error_title'] = "Restriction";
                break;

            case self::ERROR_USER_DATA_WRONG:
                $data['error_title'] = "Error due to wrong or missing data in database";
                break;
        }

        $this->data = $data;
    }
}
