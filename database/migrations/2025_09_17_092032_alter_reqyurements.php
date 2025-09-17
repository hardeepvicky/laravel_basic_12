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
        $this->dropColumnsWithIndex("requirements", ["make_id"]);

        Schema::table('requirements', function (Blueprint $table) {
            $table->foreignId("item_category_id")->after("sub_dept_id")->constrained("item_categories")->onDelete("restrict");
            $table->foreignId("item_brand_id")->after("item_id")->nullable()->constrained("item_brands")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("requirements", ["item_category_id", "item_brand_id"]);
    }
};
