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
            $table->float("gross_length")->after("height")->nullable()->default(0);
            $table->float("gross_width")->after("gross_length")->nullable()->default(0);
            $table->float("gross_height")->after("gross_width")->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("products", ["gross_length", "gross_width", "gross_height"]);
    }
};
