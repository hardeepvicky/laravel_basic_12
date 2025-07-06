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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string("main_model");
            $table->string("main_table");
            $table->string("main_id");
            $table->string("name");
            $table->string("search_term");
            $table->unsignedBigInteger("foreign_id")->nullable();
            $table->string("foreign_table")->nullable();
            $table->string("foreign_model")->nullable();
            $table->timestamps();
            $table->bigInteger("created_by")->nullable();
            $table->bigInteger("updated_by")->nullable();

            $table->index(['main_model', 'main_table','main_id']);
            $table->index(['foreign_model', 'foreign_table','foreign_id']);
            $table->index('search_term');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
