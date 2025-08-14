<?php

use App\Helpers\MyMigration;

return new class extends MyMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->dropColumnsWithIndex("parties", ["address"]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
