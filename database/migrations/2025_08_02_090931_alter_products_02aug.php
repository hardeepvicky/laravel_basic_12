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
        Schema::table('products', function (Blueprint $table) {
            $table->longText("description_html")->nullable()->after("description");
            $table->float("length")->default(0)->after('description_html');
            $table->float("width")->default(0)->after("length");
            $table->float("height")->default(0)->after("width");
            $table->float("net_weight")->default(0)->after("height");
            $table->float("gross_weight")->default(0)->after("net_weight");
            $table->string("sku")->default("")->after("gross_weight");
            $table->string("seo_title", 80)->nullable()->after("sku");
            $table->string("meta_main_keyword", 45)->nullable()->after("seo_title");
            $table->text("meta_description", 255)->nullable()->after("meta_main_keyword");
        });

        $this->dropColumnsWithIndex("products", ["short_name"]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("products", [
            "description_html",
            "length",
            "width",
            "height",
            "net_weight",
            "gross_weight",
            "sku",
            "seo_title",
            "meta_main_keyword",
            "meta_description",
        ]);
    }
};
