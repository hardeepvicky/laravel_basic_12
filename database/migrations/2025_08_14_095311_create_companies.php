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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("tag_line")->nullable();
            $table->string("email")->nullable();
            $table->string("addr1")->nullable();
            $table->string("addr2")->nullable();
            $table->string("mobile", 15)->nullable();
            $table->string("phone_no", 15)->nullable();
            $table->string("gst_no")->nullable();
            $table->string("pan_no")->nullable();
            $table->text("description")->nullable();
            $table->boolean("is_active")->default(1);
            $table->integer("list_display_order")->default(0);
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
        Schema::dropIfExists('companies');
    }
};
