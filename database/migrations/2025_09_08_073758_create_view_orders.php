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
        CREATE OR REPLACE VIEW `view_orders` AS
            SELECT 
                `orders`.`id` AS `id`,
                `orders`.`type` AS `type`,
                `orders`.`ref_id` AS `ref_id`,
                `o`.`order_no` AS `order_no`,
                `o`.`order_from_type` AS `order_from_type`,
                `o`.`status` AS `status`,
                `o`.`estimated_dispatch_date` AS `estimated_dispatch_date`,
                `o`.`dispatch_date_time` AS `dispatch_date_time`,
                `o`.`created_at` AS `created_at`,
                `o`.`created_by` AS `created_by`,
                `o`.`contact_ref_id` AS `contact_ref_id`
            FROM
                ((SELECT 
                    'sale' AS `type`,
                        `so`.`id` AS `id`,
                        `so`.`order_no` AS `order_no`,
                        `so`.`order_from_type` AS `order_from_type`,
                        `so`.`status` AS `status`,
                        `so`.`estimated_dispatch_date` AS `estimated_dispatch_date`,
                        `so`.`dispatch_date_time` AS `dispatch_date_time`,
                        `so`.`created_at` AS `created_at`,
                        `so`.`created_by` AS `created_by`,
                        `c`.`ref_id` AS `contact_ref_id`
                FROM
                    (`sale_orders` `so`
                JOIN `contacts` `c` ON ((`c`.`id` = `so`.`contact_id`))) UNION SELECT 
                    'export' AS `type`,
                        `eo`.`id` AS `id`,
                        `eo`.`order_no` AS `order_no`,
                        `eo`.`order_from_type` AS `order_from_type`,
                        `eo`.`status` AS `status`,
                        `eo`.`estimated_dispatch_date` AS `estimated_dispatch_date`,
                        `eo`.`dispatch_date_time` AS `dispatch_date_time`,
                        `eo`.`created_at` AS `created_at`,
                        `eo`.`created_by` AS `created_by`,
                        `c`.`ref_id` AS `contact_ref_id`
                FROM
                    (`export_orders` `eo`
                JOIN `export_contacts` `c` ON ((`c`.`id` = `eo`.`export_contact_id`)))) `o`
                LEFT JOIN `orders` ON (((`orders`.`ref_id` = `o`.`id`)
                    AND (`orders`.`type` = `o`.`type`))))
            ORDER BY `o`.`id` DESC
        ";

        DB::statement($q);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_orders');
    }
};
