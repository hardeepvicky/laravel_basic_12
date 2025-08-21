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
        $this->dropColumnsWithIndex("products", ["product_catalog_id"]);

        Schema::dropIfExists("product_catalogs");

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId("catalog_document_file_id")->after("product_category_id")->nullable()->constrained("document_files")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("products", ["catalog_document_file_id"]);
    }
};
