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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string("ticket_no")->unique();
            $table->foreignId("product_serial_id")->constrained("product_serials")->onDelete("restrict");
            $table->string("contact_type");
            $table->foreignId("contact_id")->nullable()->constrained("contacts")->onDelete("restrict");
            $table->foreignId("export_contact_id")->nullable()->constrained("export_contacts")->onDelete("restrict");
            $table->text("remarks")->nullable();
            $table->float("charge_fee")->default(0);
            $table->boolean("is_fixed")->default(0);
            $table->timestamps();
            $table->bigInteger("created_by")->nullable();
            $table->bigInteger("updated_by")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
