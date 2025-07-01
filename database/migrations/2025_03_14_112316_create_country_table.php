<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string("name", 150);
            $table->string("sortname", 10);
            $table->string("iso3", 3)->nullable();
            $table->string("phonecode", 10);
            $table->string("currency_name", 255)->nullable();
            $table->string("capital_city", 255)->nullable();
            $table->text("timezone", 255)->nullable();
            $table->text("imt", 255)->nullable();
            $table->boolean("is_active")->default(1);
            $table->timestamps();
            $table->bigInteger("created_by")->nullable();
            $table->bigInteger("updated_by")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
