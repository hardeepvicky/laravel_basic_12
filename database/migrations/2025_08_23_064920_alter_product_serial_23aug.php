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
        
        $this->dropColumnsWithIndex("product_serials", ["product_category_id"]);

        Schema::table('product_serials', function (Blueprint $table) {
            $table->boolean("is_parent_serial_no")->default(0)->after("branch_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("product_serials", ["is_parent_serial_no"]);
    }
};
