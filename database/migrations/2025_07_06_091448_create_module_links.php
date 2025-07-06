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
        Schema::create('module_links', function (Blueprint $table) {
            $table->id();            
            $table->string("main_table");
            $table->string("main_model");
            $table->unsignedBigInteger("main_id");
            $table->string("foreign_table");
            $table->string("foreign_model");
            $table->unsignedBigInteger("foreign_id");
            $table->timestamps();

            $table->index(['main_model', 'main_table','main_id']);
            $table->index(['foreign_model', 'foreign_table','foreign_id']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_links');
    }
};
