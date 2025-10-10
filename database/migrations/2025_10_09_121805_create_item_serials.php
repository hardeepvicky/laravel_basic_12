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
        Schema::create('item_serials', function (Blueprint $table) {
            $table->id();
            $table->string("serial_type", 45);
            $table->string("serial_no")->unique();
            $table->foreignId("item_id")->constrained("items")->onDelete("restrict");
            $table->integer("package_qty");
            $table->foreignId("branch_id")->nullable()->constrained("branches")->onDelete("restrict");
            $table->foreignId("bin_id")->nullable()->constrained("bins")->onDelete("restrict");
            $table->boolean("is_in")->default(0);
            $table->boolean("is_damage")->default(0);
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
        Schema::dropIfExists('item_serials');
    }
};
