<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_order_payment_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId("purchase_order_payment_id")->constrained("purchase_order_payments")->onDelete('restrict');
            $table->foreignId("document_file_id")->constrained("document_files")->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_payment_files');
    }
};
