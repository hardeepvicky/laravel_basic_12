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
            $table->string("model_name")->nullable()->after("short_name");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("products", ["model_name"]);
    }
};
