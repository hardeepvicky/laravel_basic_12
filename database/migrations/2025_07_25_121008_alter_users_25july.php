<?php

use App\Helpers\MyMigration;
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
            $table->string("google_id")->nullable()->after("created_via");
            $table->string("apple_id")->nullable()->after("google_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("users", ["google_id", "apple_id"]);
    }
};
