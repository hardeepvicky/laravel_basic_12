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
        Schema::create('recurring_tasks', function (Blueprint $table) {
            $table->id();
            $table->string("task_type", 100);
            $table->string("recurring_type", 45)->nullable();
            $table->string("name", 255);
            $table->unsignedBigInteger("assign_to_user_id")->nullable();
            $table->text("description")->nullable();
            $table->integer("priority")->default(1);
            $table->timestamp("start_date_time")->nullable();
            $table->string("task_should_be_complete_in")->nullable();            
            $table->boolean("is_draft")->default(0);
            $table->boolean("is_send_notification")->default(0);
            $table->timestamps();
            $table->bigInteger("created_by")->nullable();
            $table->bigInteger("updated_by")->nullable();

            $table->foreign('assign_to_user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_tasks');
    }
};
