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
        Schema::create('item_brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId("item_category_id")->constrained("item_categories")->onDelete("restrict");
            $table->string("name", 80);
            $table->string("short_name", 45);
            $table->integer("list_display_order")->default(0);
            $table->boolean("is_active");
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
        Schema::dropIfExists('item_brands');
    }
};
