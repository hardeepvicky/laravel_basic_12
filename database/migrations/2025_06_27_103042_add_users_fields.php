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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger("user_category_id")->nullable()->after("type");
            $table->unsignedBigInteger("country_id")->nullable()->after("user_category_id");

            $table->foreign('user_category_id')->references('id')->on('user_categories')->onDelete('restrict');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(["user_category_id"]);
            $table->dropColumn('user_category_id');

            $table->dropForeign(["country_id"]);
            $table->dropColumn('country_id');
        });
    }
};
