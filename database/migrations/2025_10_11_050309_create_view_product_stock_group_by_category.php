<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $q = "
        CREATE OR REPLACE VIEW `view_product_stock_group_by_categories` AS
        SELECT
            PC.name,
            SUM(SP.qty) AS sum_qty
        FROM
            stock_products SP
            INNER JOIN products P ON P.id = SP.product_id
            INNER JOIN product_categories PC ON PC.id = P.product_category_id
        GROUP BY
            PC.id
        having
            sum_qty > 0;
        ";

        DB::statement($q);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
