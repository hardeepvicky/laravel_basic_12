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
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId("origin_id")->after("main_image_id")->nullable()->constrained("drop_down_values")->onDelete("restrict");
            $table->foreignId("certification_id")->after("origin_id")->nullable()->constrained("drop_down_values")->onDelete("restrict");            
            $table->string("part_no")->nullable()->after("sku");
            $table->float("weight")->nullable()->after("part_no");
            $table->float("minimum_order_qty")->nullable()->after("weight");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("items", ["origin_id", "certification_id", "part_no", "weight", "minimum_order_qty"]);
    }
};
