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
        Schema::create('item_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("item_id")->constrained("items")->onDelete('restrict');
            $table->foreignId("sub_dept_id")->constrained("sub_departments")->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_departments');
    }
};
