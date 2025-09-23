<?php

namespace App\Models;

class EmailTemplateFile extends BaseModel
{
    protected static Array $unique_fields = ["email_template_id", "document_file_id"];

    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function documentFile()
    {
        return $this->belongsTo(DocumentFile::class);
    }

    public function getRefDisplayName($field, $value)
    {
        $ret_array = [];

        switch($field)
        {
            case "email_template_id":
                $ret_array = [
                    $field => $this->emailTemplate->getDisplayName()
                ];
                break;

            case "document_file_id":
                $ret_array = [
                    $field => $this->documentFile->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
