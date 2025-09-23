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
        $this->dropColumnsWithIndex("exp_countries", ["tld", "native", "region_id", "subregion_id"]);
        $this->dropColumnsWithIndex("exp_states", ["fips_code", "level", "parent_id", "native"]);
        $this->dropColumnsWithIndex("exp_cities", ["timezone"]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
