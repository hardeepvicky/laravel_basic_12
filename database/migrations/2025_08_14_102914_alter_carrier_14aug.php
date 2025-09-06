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
        Schema::table('carriers', function (Blueprint $table) {
            if (!Schema::hasColumn('carriers', 'state_id'))
            {
                $table->foreignId("state_id")->after("name")->nullable()->constrained("states")->onDelete("restrict");
            }
            if (!Schema::hasColumn('carriers', 'district_id'))
            {
                $table->foreignId("district_id")->after("state_id")->nullable()->constrained("districts")->onDelete("restrict");
            }
            if (!Schema::hasColumn('carriers', 'city_id'))
            {
                $table->foreignId("city_id")->after("district_id")->nullable()->constrained("cities")->onDelete("restrict");
            }
            if (!Schema::hasColumn('carriers', 'company_name'))
            {
                $table->string("company_name")->after("city_id")->nullable();
            }
            if (!Schema::hasColumn('carriers', 'email'))
            {
                $table->string("email")->after("phone_no")->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("parties", ["state_id", "district_id", "city_id", "company_name", "email"]);
    }
};
