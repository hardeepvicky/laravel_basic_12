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
        Schema::table('targets', function (Blueprint $table) {
            $table->foreignId("dept_id")->nullable()->after("id")->constrained("departments")->onDelete("restrict");
            $table->boolean("is_active")->default(0)->after("list_display_order");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("targets", ["dept_id", "is_active"]);
    }
};
