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
        $this->dropColumnsWithIndex("branches", ["company_id"]);

        Schema::dropIfExists('companies');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
