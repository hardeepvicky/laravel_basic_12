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
        $this->dropForeignKeyIfExist("exp_countries", ["region_id", "subregion_id"]);
        $this->dropForeignKeyIfExist("exp_states", ["country_id"]);
        $this->dropForeignKeyIfExist("exp_cities", ["country_id", "state_id"]);

        Schema::table('exp_countries', function (Blueprint $table) {
            $table->unsignedBigInteger("id")->change();
        });

        Schema::table('exp_states', function (Blueprint $table) {
            $table->unsignedBigInteger("id")->change();
            $table->unsignedBigInteger("country_id")->change();
        });

        Schema::table('exp_cities', function (Blueprint $table) {
            $table->unsignedBigInteger("id")->change();
            $table->unsignedBigInteger("state_id")->change();
            $table->unsignedBigInteger("country_id")->change();
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
