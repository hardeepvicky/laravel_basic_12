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
        Schema::table('leads', function (Blueprint $table) {
            $table->text("url")->after("name")->nullable();
            $table->string("image")->after("url")->nullable();
            $table->string("video")->after("image")->nullable();

            $table->string("name")->nullable()->change();
            $table->string("mobile")->nullable()->change();
            $table->unsignedBigInteger("source_id")->nullable()->change();
            $table->unsignedBigInteger("state_id")->nullable()->change();
            $table->unsignedBigInteger("district_id")->nullable()->change();
            $table->unsignedBigInteger("city_id")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("leads", ["url", "image", "video"]);
    }
};
