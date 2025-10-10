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
        Schema::create('item_brand_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("item_id")->constrained("items")->onDelete('restrict');
            $table->foreignId("make_id")->constrained("item_brands")->onDelete('restrict');
            $table->string("part_no");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_brand_parts');
    }
};
