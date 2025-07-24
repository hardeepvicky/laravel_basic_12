<?php

use App\Helpers\MyMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends MyMigration
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
        $this->dropColumnsWithIndex("users", ["person_name", "avatar", "created_via", "is_secure_login"]);
    }
};
