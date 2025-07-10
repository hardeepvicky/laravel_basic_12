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
        $this->dropIndexIfExist('module_links', ['main_model', 'main_table','main_id']);

        Schema::table('module_links', function (Blueprint $table) {

            $table->string("main_table_field")->nullable()->after("main_model");

            $table->index(['main_model', 'main_table', 'main_table_field','main_id'], "main_index");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("module_links", ["main_table_field"]);
    }
};
