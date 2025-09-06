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
        CREATE  OR REPLACE VIEW `export_view_contacts` AS
            SELECT
                `contacts`.*,
                `exp_states`.`name` AS `state_name`,
                `exp_countries`.`name` AS `country_name`,
                `exp_cities`.`name` AS `city_name`
            FROM
                ((((SELECT 
                    'lead' AS `type`,
                        `exp_leads`.`id` AS `id`,
                        `exp_leads`.`name` AS `name`,
                        `exp_leads`.`mobile` AS `mobile`,
                        `exp_leads`.`mobile2` AS `mobile2`,
                        `exp_leads`.`email` AS `email`,
                        `exp_leads`.`state_id` AS `state_id`,
                        `exp_leads`.`country_id` AS `country_id`,
                        `exp_leads`.`city_id` AS `city_id`,
                        `exp_leads`.`status_type` AS `status_type`,
                        `exp_leads`.`action_type` AS `action_type`,
                        `exp_leads`.`response_type_id` AS `response_type_id`,
                        `exp_leads`.`remarks` AS `remarks`,
                        `exp_leads`.`next_followup_date_time`,
                        `exp_leads`.`last_followup_date_time`,
                        1 AS `is_active`,
                        `exp_leads`.`created_at` AS `created_at`,
                        `exp_leads`.`created_by` AS `created_by`
                FROM
                    `exp_leads`
                WHERE
                    (`exp_leads`.`is_customer` = 0) UNION SELECT 
                    'customer' AS `type`,
                        `exp_customers`.`id` AS `id`,
                        `exp_customers`.`name` AS `name`,
                        `exp_customers`.`mobile` AS `mobile`,
                        `exp_customers`.`mobile2` AS `mobile2`,
                        `exp_customers`.`email` AS `email`,
                        `exp_customers`.`state_id` AS `state_id`,
                        `exp_customers`.`country_id` AS `country_id`,
                        `exp_customers`.`city_id` AS `city_id`,
                        `exp_customers`.`status_type` AS `status_type`,
                        `exp_customers`.`action_type` AS `action_type`,
                        `exp_customers`.`response_type_id` AS `response_type_id`,
                        `exp_customers`.`remarks` AS `remarks`,
                        `exp_customers`.`next_followup_date_time`,
                        `exp_customers`.`last_followup_date_time`,
                        1 AS `is_active`,
                        `exp_customers`.`created_at` AS `created_at`,
                        `exp_customers`.`created_by` AS `created_by`
                FROM
                    `exp_customers` UNION SELECT 
                    'party' AS `type`,
                        `exp_parties`.`id` AS `id`,
                        `exp_parties`.`name` AS `name`,
                        `exp_parties`.`mobile` AS `mobile`,
                        `exp_parties`.`mobile2` AS `mobile2`,
                        '' AS `email`,
                        `exp_parties`.`state_id` AS `state_id`,
                        `exp_parties`.`country_id` AS `country_id`,
                        `exp_parties`.`city_id` AS `city_id`,
                        `exp_parties`.`status_type` AS `status_type`,
                        `exp_parties`.`action_type` AS `action_type`,
                        `exp_parties`.`response_type_id` AS `response_type_id`,
                        `exp_parties`.`remarks` AS `remarks`,
                        `exp_parties`.`next_followup_date_time` AS `next_followup_date_time`,
                        `exp_parties`.`last_followup_date_time` AS `last_followup_date_time`,
                        `exp_parties`.`is_active` AS `is_active`,
                        `exp_parties`.`created_at` AS `created_at`,
                        `exp_parties`.`created_by` AS `created_by`
                FROM
                    `exp_parties` UNION SELECT 
                    'dealer' AS `type`,
                        `exp_dealers`.`id` AS `id`,
                        `exp_dealers`.`name` AS `name`,
                        `exp_dealers`.`mobile` AS `mobile`,
                        `exp_dealers`.`mobile2` AS `mobile2`,
                        `exp_dealers`.`email` AS `email`,
                        `exp_dealers`.`state_id` AS `state_id`,
                        `exp_dealers`.`country_id` AS `country_id`,
                        `exp_dealers`.`city_id` AS `city_id`,
                        `exp_dealers`.`status_type` AS `status_type`,
                        `exp_dealers`.`action_type` AS `action_type`,
                        `exp_dealers`.`response_type_id` AS `response_type_id`,
                        `exp_dealers`.`remarks` AS `remarks`,
                        `exp_dealers`.`next_followup_date_time` AS `next_followup_date_time`,
                        `exp_dealers`.`last_followup_date_time` AS `last_followup_date_time`,
                        `exp_dealers`.`is_active` AS `is_active`,
                        `exp_dealers`.`created_at` AS `created_at`,
                        `exp_dealers`.`created_by` AS `created_by`
                FROM
                    `exp_dealers` UNION SELECT 
                    'competitor' AS `type`,
                        `exp_competitors`.`id` AS `id`,
                        `exp_competitors`.`name` AS `name`,
                        `exp_competitors`.`mobile` AS `mobile`,
                        `exp_competitors`.`mobile2` AS `mobile2`,
                        `exp_competitors`.`email` AS `email`,
                        `exp_competitors`.`state_id` AS `state_id`,
                        `exp_competitors`.`country_id` AS `country_id`,
                        `exp_competitors`.`city_id` AS `city_id`,
                        '' AS `status_type`,
                        '' AS `action_type`,
                        '' AS `response_type_id`,
                        '' AS `remarks`,
                        NOW() AS `next_followup_date_time`,
                        NOW() AS `last_followup_date_time`,
                        `exp_competitors`.`is_active` AS `is_active`,
                        `exp_competitors`.`created_at` AS `created_at`,
                        `exp_competitors`.`created_by` AS `created_by`
                FROM
                    `exp_competitors`) `contacts`
                JOIN `exp_states` ON ((`exp_states`.`id` = `contacts`.`state_id`)))
                JOIN `exp_countries` ON ((`exp_countries`.`id` = `contacts`.`country_id`)))
                JOIN `exp_cities` ON ((`exp_cities`.`id` = `contacts`.`city_id`)))
            ORDER BY `contacts`.`name`;
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
