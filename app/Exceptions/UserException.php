<?php

namespace App\Exceptions;

use Exception;

class UserException extends Exception
{
    public const ERROR_GENERAL = 512;

    public $data;
    public function __construct(String $message, int $code = 512, $data = [])
    {
        parent::__construct($message, $code);
        $this->setData($data);
    }

    public function setData($data)
    {
        switch ($this->code) {
            case self::ERROR_GENERAL:
                $data = [
                    "html" => "This is Error due to Developer's mistake",
                ];
                break;
        }

        $this->data = $data;
    }
}
