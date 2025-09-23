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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("item_category_id")->constrained("item_categories")->onDelete("restrict");
            $table->string("name");
            $table->text("description")->nullable();
            $table->foreignId("color_id")->nullable()->constrained("drop_down_values")->onDelete("restrict");
            $table->string("size")->nullable();
            $table->foreignId("brand_id")->nullable()->constrained("drop_down_values")->onDelete("restrict");
            $table->string("specification")->nullable();
            $table->string("sku");
            $table->foreignId("main_image_id")->nullable()->constrained("document_files")->onDelete("restrict");
            $table->boolean("is_active")->default(1);
            $table->integer("list_display_order")->default(0);
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
        Schema::dropIfExists('items');
    }
};
