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
        Schema::create('exp_party_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId("exp_party_id")->constrained("exp_parties")->onDelete("restrict");
            $table->string("name", 512);
            $table->boolean("is_active")->default(1);
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
        Schema::dropIfExists('exp_party_terms');
    }
};
