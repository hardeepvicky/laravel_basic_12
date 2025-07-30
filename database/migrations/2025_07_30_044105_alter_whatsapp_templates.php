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
        Schema::table('whatsapp_templates', function (Blueprint $table) {
            $table->string("template_name")->default("")->after("name");

            $table->index(["name"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("whatsapp_templates", ["google_id", "apple_id"]);
        $this->dropIndexIfExist("whatsapp_templates", ["name"]);
    }
};
