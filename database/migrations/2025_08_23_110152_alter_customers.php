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
        Schema::table('customers', function (Blueprint $table) {
            $table->string("deal_type", 80)->nullable()->after("city_id");
            $table->foreignId("dealer_id")->nullable()->after("deal_type")->constrained("dealers")->onDelete("restrict");
            $table->foreignId("assign_to_user_id")->nullable()->after("dealer_id")->constrained("users")->onDelete("restrict");
            $table->foreignId("response_type_id")->nullable()->after("assign_to_user_id")->constrained("users")->onDelete("restrict");
            $table->string("status", 80)->nullable()->after("response_type_id");
            $table->dateTime("next_meeting_date_time")->nullable()->after("status");
            $table->boolean("is_snooze")->default(0)->after("next_meeting_date_time");
            $table->integer("snooze_minute")->default(0)->after("is_snooze");
            $table->dateTime("snooze_datetime")->nullable()->after("snooze_minute");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("customers", ["deal_type", "dealer_id", "assign_to_user_id", "response_type_id", "status", "next_meeting_date_time", "is_snooze", "snooze_minute", "snooze_datetime"]);
    }
};
