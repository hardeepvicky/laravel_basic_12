<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("parent_id")->default(0);
            $table->string("name");
            $table->string("css_class")->nullable();
            $table->string("icon_css_class")->nullable();
            $table->string("route_name")->nullable();
            $table->text("url_params")->nullable();
            $table->unsignedBigInteger("department_id")->nullable();            
            $table->boolean("is_link")->default(0);
            $table->boolean("is_active")->default(0);
            $table->integer("menu_display_order")->default(0);
            $table->timestamps();
            $table->bigInteger("created_by")->nullable();
            $table->bigInteger("updated_by")->nullable();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_menus');
    }
};
