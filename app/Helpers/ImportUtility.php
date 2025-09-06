<?php
namespace App\Helpers;

use Maatwebsite\Excel\Facades\Excel;

class ImportUtility
{
    public $errors = array(), $post_data, $sample_data;
    private $sample_csv_file;
    
    public function __construct($sample_file, $post_file, $max_count = 50000) 
    {
        $this->sample_csv_file = $sample_file;

        $sample_csv_utility = new CsvUtility($this->sample_csv_file);
        
        $this->sample_data = $sample_csv_utility->find();

        $ext = strtolower(pathinfo($post_file, PATHINFO_EXTENSION));

        if ($ext == "csv")
        {
            $post_csv_utility = new CsvUtility($post_file);

            $this->post_data = $post_csv_utility->find(); 
        }
        else if ($ext == "xlsx")
        {
            $excel_data = Excel::toArray([], $post_file);

            $header_row = [];

            $this->post_data = [];

            foreach($excel_data[0] as $index => $row)
            {
                if (empty($header_row) && $index == 0)
                {
                    $header_row = $row;
                }
                else
                {
                    $record = [];

                    foreach($row as $k => $v)
                    {
                        if (isset($header_row[$k]))
                        {
                            $record[$header_row[$k]] = $v;
                        }
                        else
                        {
                            $record[$k] = $v;
                        }
                    }

                    $this->post_data[] = $record;
                }
            }
        }

        if (count($this->post_data) > $max_count)
        {
            throw new \Exception("CSV Data excced the limit of $max_count records in single file");
        }
    }
    
    public function checkHeaders()
    {
        $this->errors = array();

        if (!isset($this->post_data[0]))
        {
            $this->errors[] = "Post File is empty";
            return false;
        }
        
        $first_row = $this->post_data[0];

        foreach($this->sample_data[0] as $col => $value)
        {
            $col = trim($col);
            $col_2 = Util::StringOpearion($col, array("strtolower", "replace_multple_space_with_single_space", "replace_space_with_hyphine"));
            if (!isset($first_row[$col]) && !isset($first_row[$col_2]))
            {
                $this->errors[] = "Column $col not found";
            }
        }

        return empty($this->errors);
    }
    
    public function replaceHeaders($new_headers)
    {
        $data = array();
        
        foreach($this->post_data as $row)
        {
            $record = array();
            foreach($row as $col => $v)
            {
                $col = trim($col);
                
                if (isset($new_headers[$col]))
                {
                    $record[$new_headers[$col]] = $v;
                }
                else
                {
                    $col = Util::StringOpearion($col, array("strtolower", "replace_multple_space_with_single_space", "replace_space_with_hyphine"));
                    if (isset($new_headers[$col]))
                    {
                        $record[$new_headers[$col]] = $v;
                    }
                }
            }
            
            $data[] = $record;
        }
        
        return $data;
    }
}