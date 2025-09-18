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
        $this->dropColumnsWithIndex("items", ["item_category_id", "color_id", "brand_id"]);
        
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId("root_item_category_id")->after("id")->nullable()->constrained("item_categories")->onDelete("restrict");
            $table->foreignId("parent_item_category_id")->after("root_item_category_id")->nullable()->constrained("item_categories")->onDelete("restrict");
            $table->foreignId("child_item_category_id")->after("parent_item_category_id")->nullable()->constrained("item_categories")->onDelete("restrict");
            $table->foreignId("item_brand_id")->after("child_item_category_id")->nullable()->constrained("item_brands")->onDelete("restrict");
            $table->foreignId("dept_id")->after("item_brand_id")->nullable()->constrained("departments")->onDelete("restrict");
            $table->boolean("is_for_all_sub_dept")->default(0)->after("sku");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("items", ["root_item_category_id", "parent_item_category_id", "child_item_category_id", "item_brand_id", "dept_id", "is_for_all_sub_dept"]);
    }
};
