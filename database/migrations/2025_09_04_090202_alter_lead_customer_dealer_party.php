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
        Schema::table('leads', function (Blueprint $table) {
            $table->dateTime("last_followup_date_time")->nullable()->after("next_meeting_date_time");
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dateTime("last_followup_date_time")->nullable()->after("next_meeting_date_time");
        });

        Schema::table('dealers', function (Blueprint $table) {
            $table->dateTime("last_followup_date_time")->nullable()->after("next_followup_date_time");
        });

        Schema::table('parties', function (Blueprint $table) {
            $table->dateTime("last_followup_date_time")->nullable()->after("next_followup_date_time");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("leads", [
            "last_followup_date_time",
        ]);

        $this->dropColumnsWithIndex("customers", [
            "last_followup_date_time",
        ]);

        $this->dropColumnsWithIndex("dealers", [
            "last_followup_date_time",
        ]);

        $this->dropColumnsWithIndex("parties", [
            "last_followup_date_time",
        ]);
    }
};
