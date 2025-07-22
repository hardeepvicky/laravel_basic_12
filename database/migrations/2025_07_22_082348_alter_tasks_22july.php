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
        Schema::table('tasks', function (Blueprint $table) {
            $table->datetime("complete_date_time")->nullable()->after("is_completed");
            $table->unsignedBigInteger("complete_by")->nullable()->after("complete_date_time");
            $table->datetime("verified_date_time")->nullable()->after("is_verified");
            $table->unsignedBigInteger("verified_by")->nullable()->after("verified_date_time");
            $table->float("rating")->nullable()->after("is_send_notification");
            $table->text("review_remarks")->nullable()->after("rating");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("tasks", ["complete_date_time", "complete_by", "verified_date_time", "verified_by", "rating", "review_remarks"]);
    }
};
