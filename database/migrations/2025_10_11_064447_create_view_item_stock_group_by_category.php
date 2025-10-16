<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $q = "
        CREATE  OR REPLACE VIEW `view_item_stock_group_by_categories` AS
        SELECT
            IC.name,
            U.short_name as unit,
            SUM(SI.qty) AS sum_qty
        FROM
            stock_items SI
            INNER JOIN items I ON I.id = SI.item_id
            INNER JOIN item_categories IC ON IC.id = I.root_item_category_id
            INNER JOIN uoms U ON U.id = IC.base_uom_id
        GROUP BY
            IC.id, U.id
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
