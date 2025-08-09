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
            $table->dateTime("next_meeting_date_time")->nullable()->after("assign_to_user_id");
            
            $table->index(["next_meeting_date_time"]);
            $table->index(["status_type"]);
            $table->index(["mobile"]);
            $table->index(["source_type"]);
            $table->index(["deal_type"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("leads", ["next_meeting_date_time"]);

        $this->dropIndexIfExist("leads", ["status_type", "mobile", "source_type", "deal_type"]);
    }
};
