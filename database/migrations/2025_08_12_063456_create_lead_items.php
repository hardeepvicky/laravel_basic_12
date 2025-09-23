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
        Schema::create('lead_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("lead_id")->constrained("leads")->onDelete("restrict");
            $table->foreignId("item_id")->constrained("items")->onDelete("restrict");
            $table->foreignId("uom_id")->constrained("uoms")->onDelete("restrict");
            $table->float("qty");
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
        Schema::dropIfExists('lead_items');
    }
};
