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
            $table->string("person_name")->nullable()->after("name");
            $table->string("avatar")->nullable()->after("profile_image");
            $table->string("created_via", 45)->nullable()->after("profile_image");
            $table->boolean("is_secure_login")->default(0)->after("is_active");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('person_name');
            $table->dropColumn('avatar');
            $table->dropColumn('created_via');
            $table->dropColumn('is_secure_login');
        });
    }
};
