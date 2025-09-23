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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId("installation_guide_document_file_id")->after("catalog_document_file_id")->nullable()->constrained("document_files")->onDelete("restrict");
            $table->foreignId("user_manual_document_file_id")->after("installation_guide_document_file_id")->nullable()->constrained("document_files")->onDelete("restrict");
            $table->foreignId("troubleshoot_document_file_id")->after("user_manual_document_file_id")->nullable()->constrained("document_files")->onDelete("restrict");
            $table->string("tab_name")->nullable()->after("list_display_order");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("products", ["installation_guide_document_file_id", "user_manual_document_file_id", "troubleshoot_document_file_id", "tab_name"]);
    }
};
