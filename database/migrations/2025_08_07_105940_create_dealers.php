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
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->string("type");
            $table->string("name");
            $table->string("mobile", 15);
            $table->string("mobile2", 15)->nullable();
            $table->string("phone_no", 15)->nullable();
            $table->string("email")->nullable();
            $table->foreignId("state_id")->constrained("states")->onDelete("restrict");
            $table->foreignId("district_id")->constrained("districts")->onDelete("restrict");
            $table->foreignId("city_id")->constrained("cities")->onDelete("restrict");
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
        Schema::dropIfExists('dealers');
    }
};
