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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger("dept_id")->nullable()->after("user_category_id");
            $table->unsignedBigInteger("sub_dept_id")->nullable()->after("dept_id");
            $table->unsignedBigInteger("desg_id")->nullable()->after("sub_dept_id");

            $table->foreign('dept_id')->references('id')->on('departments')->onDelete('restrict');
            $table->foreign('sub_dept_id')->references('id')->on('sub_departments')->onDelete('restrict');
            $table->foreign('desg_id')->references('id')->on('designations')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("users", ["dept_id", "sub_dept_id", "desg_id"]);
    }
};
