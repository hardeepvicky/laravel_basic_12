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
        Schema::table('exp_customers', function (Blueprint $table) {
            $table->string("action_type")->nullable()->after("snooze_datetime");
            $table->dateTime("next_followup_date_time")->nullable()->after("action_type");
            $table->dateTime("last_followup_date_time")->nullable()->after("next_followup_date_time");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("exp_customers", [
            "action_type",
            "next_followup_date_time",
            "last_followup_date_time",
        ]);
    }
};
