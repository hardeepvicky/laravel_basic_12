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
        Schema::create('exp_competitor_address', function (Blueprint $table) {
            $table->id();
            $table->foreignId("exp_competitor_id")->constrained("exp_competitors")->onDelete("restrict");
            $table->string("type", 45);
            $table->string("addr1");
            $table->string("addr2")->nullable();
            $table->string("pincode", 10);
            $table->boolean("is_primary")->default(0);
            $table->integer("list_display_order")->default(1);
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
        Schema::dropIfExists('exp_competitor_address');
    }
};
