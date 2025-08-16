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
            $table->boolean("is_snooze")->default(0)->after("next_meeting_date_time");
            $table->integer("snooze_minute")->default(0)->after("is_snooze");
            $table->datetime("snooze_datetime")->nullable()->after("snooze_minute");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("leads", ["is_snooze", "snooze_minute", "snooze_datetime"]);
    }
};
