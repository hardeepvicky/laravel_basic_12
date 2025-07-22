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
        Schema::table('product_category_attributes', function (Blueprint $table) {
            $table->foreignId("uom_id")->nullable()->after("dropdown_type")->constrained("uoms")->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("product_category_attributes", ["uom_id"]);
    }
};
