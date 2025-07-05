<?php

namespace App\View\Components\Backend;

use App\Exceptions\UserException;
use App\View\Components\BackendComponent;

class IndexTableInfo extends BackendComponent
{
    public function __construct(
        public $record,
        public $userList,
        public $options = []
    )
    {
        parent::__construct();

        $default_options = [
            "show_as" => "celec_datetime"
        ];

        $show_as_valid_options = ["celec_datetime", "datetime", "date", "full_datetime", "human_date"];

        $options = array_merge($default_options, $options);

        if (!isset($options['show_as']))
        {
            throw new UserException("show_as key is not found in options");
        }

        UserException::checkValueExistOrThrowException($show_as_valid_options, $options['show_as']);

        $this->setForView(compact("options"));
        
        $this->view_path = "index_table_info";
    }
}
