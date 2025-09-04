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
        $this->dropColumnsWithIndex("customers", [
            "response_type_id",
        ]);

        Schema::table('customers', function (Blueprint $table) {
            $table->string("action_type", 45)->nullable()->after("assign_to_user_id");
            $table->foreignId("response_type_id")->after("action_type")->nullable()->constrained("drop_down_values")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("customers", [
            "action_type",
            "response_type_id"
        ]);
    }
};
