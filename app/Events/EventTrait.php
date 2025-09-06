<?php
namespace App\Events;

trait EventTrait
{
    public function getData() : array
    {
        $data = $this->model->toArray();

        foreach($data as $field => $value)
        {
            if (strpos($field, "_id") > 0)
            {
                if (method_exists($this->model, "getRefDisplayName"))
                {
                    $ret_arr = $this->model->getRefDisplayName($field, $value);

                    $data = array_merge($data, $ret_arr);
                }
            }
        }

        return $data;
    }
}