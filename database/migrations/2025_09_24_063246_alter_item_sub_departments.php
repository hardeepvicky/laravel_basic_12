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
        $this->dropColumnsWithIndex("item_sub_departments", ["sub_department_id"]);

        Schema::table('item_sub_departments', function (Blueprint $table) {
            $table->foreignId("dept_id")->after("item_id")->nullable()->constrained("departments")->onDelete("restrict");
            $table->text("sub_dept_id")->nullable()->after("dept_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("item_sub_departments", ["dept_id", "sub_dept_id"]);
    }
};
