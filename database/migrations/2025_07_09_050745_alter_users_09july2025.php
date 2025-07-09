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
        $this->dropColumnsWithIndex("users", ["used_in_other_table_created_by", "used_in_other_table_updated_by"]);

        Schema::table('users', function (Blueprint $table) {
            $table->string("login_timezone")->nullable()->after("is_secure_login");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("users", ["login_timezone"]);
    }
};
