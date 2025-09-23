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
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("branch_id")->constrained("branches")->onDelete('restrict');
            $table->foreignId("bin_id")->nullable()->constrained("bins")->onDelete('restrict');
            $table->foreignId("item_id")->constrained("items")->onDelete('restrict');
            $table->foreignId("brand_id")->constrained("drop_down_values")->onDelete('restrict');
            $table->float("opening_qty");
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
        Schema::dropIfExists('stock_items');
    }
};
