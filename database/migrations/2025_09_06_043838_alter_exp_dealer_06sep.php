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
        Schema::table('exp_dealers', function (Blueprint $table) {
            $table->dateTime("last_followup_date_time")->nullable()->after("next_followup_date_time");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("exp_dealers", [
            "last_followup_date_time",
        ]);
    }
};
