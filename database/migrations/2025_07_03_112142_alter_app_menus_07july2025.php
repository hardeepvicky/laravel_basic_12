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
        Schema::table('app_menus', function (Blueprint $table) {
            $table->text("show_in_static_menu")->nullable()->after("is_active");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_menus', function (Blueprint $table) {
            $table->dropColumn('show_in_static_menu');
        });
    }
};
