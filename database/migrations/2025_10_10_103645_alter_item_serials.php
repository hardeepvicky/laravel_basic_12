<?php

use App\Helpers\MyMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends MyMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('item_serials', function (Blueprint $table) {
            $table->foreignId("item_brand_id")->after("item_id")->nullable()->constrained("item_brands")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("item_serials", ["item_brand_id"]);
    }
};
