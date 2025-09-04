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
            CREATE OR REPLACE VIEW `view_contacts` AS
                SELECT 
                    `contacts`.*,
                    `states`.`name` AS `state_name`,
                    `districts`.`name` AS `district_name`,
                    `cities`.`name` AS `city_name`
                FROM
                    ((((SELECT 
                        'lead' AS `type`,
                            `leads`.`id` AS `id`,
                            `leads`.`name` AS `name`,
                            `leads`.`mobile` AS `mobile`,
                            `leads`.`mobile2` AS `mobile2`,
                            `leads`.`email` AS `email`,
                            `leads`.`state_id` AS `state_id`,
                            `leads`.`district_id` AS `district_id`,
                            `leads`.`city_id` AS `city_id`,
                            `leads`.`status_type` AS `status_type`,
                            `leads`.`action_type` AS `action_type`,
                            `leads`.`response_type_id` AS `response_type_id`,
                            `leads`.`remarks` AS `remarks`,
                            `leads`.`next_meeting_date_time` AS `next_followup_date_time`,
                            `leads`.`last_followup_date_time` AS `last_followup_date_time`,                
                            1 AS `is_active`,
                            leads.created_at,
                            leads.created_by
                    FROM
                        `leads`
                    WHERE
                        (`leads`.`is_customer` = 0) UNION SELECT 
                        'customer' AS `type`,
                            `customers`.`id` AS `id`,
                            `customers`.`name` AS `name`,
                            `customers`.`mobile` AS `mobile`,
                            `customers`.`mobile2` AS `mobile2`,
                            `customers`.`email` AS `email`,
                            `customers`.`state_id` AS `state_id`,
                            `customers`.`district_id` AS `district_id`,
                            `customers`.`city_id` AS `city_id`,
                            `customers`.`status_type` AS `status_type`,
                            `customers`.`action_type` AS `action_type`,
                            `customers`.`response_type_id` AS `response_type_id`,
                            `customers`.`remarks` AS `remarks`,
                            `customers`.`next_meeting_date_time` AS `next_followup_date_time`,
                            `customers`.`last_followup_date_time` AS `last_followup_date_time`,                
                            1 AS `is_active`,
                            customers.created_at,
                            customers.created_by
                    FROM
                        `customers` UNION SELECT 
                        'party' AS `type`,
                            `parties`.`id` AS `id`,
                            `parties`.`name` AS `name`,
                            `parties`.`mobile` AS `mobile`,
                            `parties`.`mobile2` AS `mobile2`,
                            '' AS `email`,
                            `parties`.`state_id` AS `state_id`,
                            `parties`.`district_id` AS `district_id`,
                            `parties`.`city_id` AS `city_id`,
                            `parties`.`status_type` AS `status_type`,
                            `parties`.`action_type` AS `action_type`,
                            `parties`.`response_type_id` AS `response_type_id`,
                            `parties`.`remarks` AS `remarks`,
                            `parties`.`next_followup_date_time` AS `next_followup_date_time`,
                            `parties`.`last_followup_date_time` AS `last_followup_date_time`,
                            `parties`.`is_active` AS `is_active`,
                            parties.created_at,
                            parties.created_by
                    FROM
                        `parties` UNION SELECT 
                        'dealer' AS `type`,
                            `dealers`.`id` AS `id`,
                            `dealers`.`name` AS `name`,
                            `dealers`.`mobile` AS `mobile`,
                            `dealers`.`mobile2` AS `mobile2`,
                            `dealers`.`email` AS `email`,
                            `dealers`.`state_id` AS `state_id`,
                            `dealers`.`district_id` AS `district_id`,
                            `dealers`.`city_id` AS `city_id`,
                            `dealers`.`status_type` AS `status_type`,
                            `dealers`.`action_type` AS `action_type`,
                            `dealers`.`response_type_id` AS `response_type_id`,
                            `dealers`.`remarks` AS `remarks`,
                            `dealers`.`next_followup_date_time` AS `next_followup_date_time`,
                            `dealers`.`last_followup_date_time` AS `last_followup_date_time`,
                            `dealers`.`is_active` AS `is_active`,
                            dealers.created_at,
                            dealers.created_by
                    FROM
                        `dealers` UNION SELECT 
                        'competitor' AS `type`,
                            `competitors`.`id` AS `id`,
                            `competitors`.`name` AS `name`,
                            `competitors`.`mobile` AS `mobile`,
                            `competitors`.`mobile2` AS `mobile2`,
                            `competitors`.`email` AS `email`,
                            `competitors`.`state_id` AS `state_id`,
                            `competitors`.`district_id` AS `district_id`,
                            `competitors`.`city_id` AS `city_id`,
                            '' AS `status_type`,
                            '' AS `action_type`,
                            '' AS `response_type_id`,
                            '' AS `remarks`,
                            NOW() AS `next_followup_date_time`,
                            NOW() AS `last_followup_date_time`,
                            `competitors`.`is_active` AS `is_active`,
                            competitors.created_at,
                            competitors.created_by
                    FROM
                        `competitors`) `contacts`
                    JOIN `states` ON ((`states`.`id` = `contacts`.`state_id`)))
                    JOIN `districts` ON ((`districts`.`id` = `contacts`.`district_id`)))
                    JOIN `cities` ON ((`cities`.`id` = `contacts`.`city_id`)))
                ORDER BY `contacts`.`name`;
        ";

        DB::statement($q);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
