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
        $this->dropColumnsWithIndex("dispatch_orders", ["carrier_id"]);

        Schema::table('dispatch_orders', function (Blueprint $table) {
            $table->foreignId("carrier_id")->nullable()->constrained("parties")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
