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
        Schema::create('product_serials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("parent_id")->default(0);
            $table->string("serial_no")->unique();
            $table->foreignId("product_category_id")->constrained("product_categories")->onDelete("restrict");
            $table->foreignId("product_id")->constrained("products")->onDelete("restrict");
            $table->foreignId("make_id")->constrained("makers")->onDelete("restrict");
            $table->foreignId("dealer_id")->nullable()->constrained("dealers")->onDelete("restrict");
            $table->foreignId("customer_id")->nullable()->constrained("customers")->onDelete("restrict");
            $table->foreignId("branch_id")->nullable()->constrained("branches")->onDelete("restrict");
            $table->boolean("is_in")->default(0);
            $table->boolean("is_sold")->default(0);
            $table->boolean("is_return")->default(0);
            $table->integer("return_count")->default(0);
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
        Schema::dropIfExists('product_serials');
    }
};
