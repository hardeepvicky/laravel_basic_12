<?php

namespace App\View\Components\Backend;

use App\Exceptions\UserException;
use App\View\Components\BackendComponent;

class CreatedAndBy extends BackendComponent
{
    public function __construct(
        public $datetime,
        public $userId,
        public $options = []
    ) {
        parent::__construct();

        $default_options = [
            "show_as" => "celec_datetime"
        ];

        $show_as_valid_options = ["celec_datetime", "datetime", "date", "full_datetime", "human_date"];

        $option_array = array_merge($default_options, $options);

        if (!isset($option_array['show_as'])) {
            throw new UserException("show_as key is not found in options");
        }

        UserException::checkValueExistOrThrowException($show_as_valid_options, $option_array['show_as']);

        $this->setForView(compact("option_array"));

        $this->view_path = "created_and_by";
    }
}
