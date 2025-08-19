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
        $this->dropIndexIfExist("branches", ["type", "name", "mobile", "phone_no", "email", "is_active", "list_display_order"]);

        Schema::table('branches', function (Blueprint $table) {
            $table->index(["type"]);
            $table->index(["name"]);
            $table->index(["mobile"]);
            $table->index(["phone_no"]);
            $table->index(["email"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
