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
        Schema::table('exp_states', function (Blueprint $table) {
            $table->integer("list_display_order")->default(0);
            $table->boolean("is_active")->default(1);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("updated_by")->nullable();

            $table->double("latitude")->nullable()->change();
            $table->double("longitude")->nullable()->change();
        });

        Schema::table('exp_cities', function (Blueprint $table) {
            $table->integer("list_display_order")->default(0);
            $table->boolean("is_active")->default(1);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("updated_by")->nullable();

            $table->double("latitude")->nullable()->change();
            $table->double("longitude")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("exp_states", [
            "list_display_order",
            "is_active",
            "created_by",
            "updated_by",
        ]);
        $this->dropColumnsWithIndex("exp_cities", [
            "list_display_order",
            "is_active",
            "created_by",
            "updated_by",
        ]);

    }
};
