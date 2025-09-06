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
        Schema::table('dealers', function (Blueprint $table) {
            $table->string("action_type", 45)->nullable()->after("description");
            $table->foreignId("response_type_id")->after("action_type")->nullable()->constrained("drop_down_values")->onDelete("restrict");
            $table->string("remarks")->nullable()->after("response_type_id");
            $table->string("status_type", 45)->nullable()->after("remarks");
            $table->dateTime("next_followup_date_time")->nullable()->after("status_type");
            $table->foreignId("assign_to_user_id")->nullable()->after("next_followup_date_time")->constrained("users")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("dealers", [
            "action_type",
            "response_type_id",
            "remarks",
            "status_type",
            "next_followup_date_time",
            "assign_to_user_id",
        ]);
    }
};
