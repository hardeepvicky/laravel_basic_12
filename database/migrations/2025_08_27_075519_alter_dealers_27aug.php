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
        Schema::table('dealers', function (Blueprint $table) {
            $table->string("alias", 45)->after("name")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("dealers", ["alias"]);
    }
};
