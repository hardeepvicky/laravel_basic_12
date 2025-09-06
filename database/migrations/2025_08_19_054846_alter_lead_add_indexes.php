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
        $this->dropIndexIfExist("leads", ["name", "mobile", "mobile2", "email", "is_snooze", "snooze_datetime"]);

        Schema::table('leads', function (Blueprint $table) {
            $table->index(["name"]);
            $table->index(["mobile"]);
            $table->index(["mobile2"]);
            $table->index(["email"]);
            $table->index(["is_snooze", "snooze_datetime"]);
        });

        $this->dropIndexIfExist("lead_histories", ["status_type", "next_meeting_date_time"]);

        Schema::table('lead_histories', function (Blueprint $table) {
            $table->index(["status_type"]);
            $table->index(["next_meeting_date_time"]);
        });

        $this->dropIndexIfExist("lead_addresses", ["type", "is_primary", "list_display_order"]);

        Schema::table('lead_addresses', function (Blueprint $table) {
            $table->index(["type"]);
            $table->index(["is_primary"]);
            $table->index(["list_display_order"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
