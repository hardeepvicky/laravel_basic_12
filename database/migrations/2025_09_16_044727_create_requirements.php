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
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->string("order_no", 45)->unique();
            $table->foreignId("branch_id")->constrained("branches")->onDelete("restrict");
            $table->foreignId("dept_id")->constrained("departments")->onDelete("restrict");
            $table->foreignId("sub_dept_id")->nullable()->constrained("sub_departments")->onDelete("restrict");
            $table->foreignId("item_id")->constrained("items")->onDelete("restrict");
            $table->foreignId("make_id")->nullable()->constrained("makers")->onDelete("restrict");
            $table->string("part_no", 45)->nullable();
            $table->float("qty");
            $table->foreignId("uom_id")->constrained("uoms")->onDelete("restrict");
            $table->string("status", 45);
            $table->text("remarks")->nullable();
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
        Schema::dropIfExists('requirements');
    }
};
