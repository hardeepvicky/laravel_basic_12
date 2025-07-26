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
        $this->dropForeignKeyIfExist("users", ["user_category_id"]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
