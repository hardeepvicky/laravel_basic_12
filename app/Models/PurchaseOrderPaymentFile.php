<?php

namespace App\Models;

class PurchaseOrderPaymentFile extends BaseModel
{
    protected static Array $unique_fields = ["purchase_order_payment_id", "document_file_id"];

    public function purchaseOrderPayment()
    {
        return $this->belongsTo(PurchaseOrderPayment::class);
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
            case "purchase_order_payment_id":
                $ret_array = [
                    $field => $this->purchaseOrderPayment?->getDisplayName()
                ];
                break;

            case "document_file_id":
                $ret_array = [
                    $field => $this->documentFile?->getDisplayName()
                ];
                break;
        }

        return $ret_array;
    }
}
