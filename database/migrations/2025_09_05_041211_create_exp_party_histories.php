<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exp_party_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId("exp_party_id")->constrained("exp_parties")->onDelete("restrict");
            $table->string("action_type", 45)->nullable();
            $table->string("action")->nullable();
            $table->foreignId("response_type_id")->nullable()->constrained("drop_down_values")->onDelete("restrict");
            $table->string("response")->nullable();
            $table->string("recording_file")->nullable();
            $table->string("status_type", 45)->nullable();
            $table->string("remarks")->nullable();
            $table->dateTime("next_followup_date_time")->nullable();
            $table->foreignId("assign_to_user_id")->nullable()->constrained("users")->onDelete("restrict");
            $table->timestamps();
            $table->bigInteger("created_by")->nullable();
            $table->bigInteger("updated_by")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exp_party_histories');
    }
};
