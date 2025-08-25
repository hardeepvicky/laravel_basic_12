/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `app_menu_related_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_menu_related_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `app_menu_id` bigint unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_name_array` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_menu_related_links_app_menu_id_foreign` (`app_menu_id`),
  CONSTRAINT `app_menu_related_links_app_menu_id_foreign` FOREIGN KEY (`app_menu_id`) REFERENCES `app_menus` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `app_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `css_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_css_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_params` text COLLATE utf8mb4_unicode_ci,
  `department_id` bigint unsigned DEFAULT NULL,
  `is_link` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_static_menu` text COLLATE utf8mb4_unicode_ci,
  `menu_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_menus_department_id_foreign` (`department_id`),
  CONSTRAINT `app_menus_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auto_serial_patterns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auto_serial_patterns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pattern` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_id` bigint unsigned NOT NULL,
  `district_id` bigint unsigned NOT NULL,
  `city_id` bigint unsigned NOT NULL,
  `addr1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addr2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branches_state_id_foreign` (`state_id`),
  KEY `branches_district_id_foreign` (`district_id`),
  KEY `branches_city_id_foreign` (`city_id`),
  KEY `branches_type_index` (`type`),
  KEY `branches_name_index` (`name`),
  KEY `branches_mobile_index` (`mobile`),
  KEY `branches_phone_no_index` (`phone_no`),
  KEY `branches_email_index` (`email`),
  KEY `branches_is_active_index` (`is_active`),
  KEY `branches_list_display_order_index` (`list_display_order`),
  CONSTRAINT `branches_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `branches_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `branches_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `campaign_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaign_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned NOT NULL,
  `document_file_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_files_campaign_id_foreign` (`campaign_id`),
  KEY `campaign_files_document_file_id_foreign` (`document_file_id`),
  CONSTRAINT `campaign_files_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `campaign_files_document_file_id_foreign` FOREIGN KEY (`document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaigns_name_index` (`name`),
  KEY `campaigns_start_date_index` (`start_date`),
  KEY `campaigns_end_date_index` (`end_date`),
  KEY `campaigns_is_active_index` (`is_active`),
  KEY `campaigns_list_display_order_index` (`list_display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district_id` bigint unsigned NOT NULL,
  `state_id` bigint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cities_district_id_foreign` (`district_id`),
  KEY `cities_state_id_foreign` (`state_id`),
  KEY `cities_name_index` (`name`),
  KEY `cities_is_active_index` (`is_active`),
  KEY `cities_list_display_order_index` (`list_display_order`),
  CONSTRAINT `cities_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `cities_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso3` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phonecode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capital_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` text COLLATE utf8mb4_unicode_ci,
  `imt` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `countries_name_index` (`name`),
  KEY `countries_short_name_index` (`short_name`),
  KEY `countries_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile2` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_gst_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_id` bigint unsigned NOT NULL,
  `district_id` bigint unsigned NOT NULL,
  `city_id` bigint unsigned NOT NULL,
  `deal_type` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dealer_id` bigint unsigned DEFAULT NULL,
  `assign_to_user_id` bigint unsigned DEFAULT NULL,
  `response_type_id` bigint unsigned DEFAULT NULL,
  `status` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_meeting_date_time` datetime DEFAULT NULL,
  `is_snooze` tinyint(1) NOT NULL DEFAULT '0',
  `snooze_minute` int NOT NULL DEFAULT '0',
  `snooze_datetime` datetime DEFAULT NULL,
  `profile_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_lead_id_foreign` (`lead_id`),
  KEY `customers_state_id_foreign` (`state_id`),
  KEY `customers_district_id_foreign` (`district_id`),
  KEY `customers_city_id_foreign` (`city_id`),
  KEY `customers_dealer_id_foreign` (`dealer_id`),
  KEY `customers_assign_to_user_id_foreign` (`assign_to_user_id`),
  KEY `customers_response_type_id_foreign` (`response_type_id`),
  CONSTRAINT `customers_assign_to_user_id_foreign` FOREIGN KEY (`assign_to_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customers_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customers_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customers_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customers_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customers_response_type_id_foreign` FOREIGN KEY (`response_type_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `customers_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dealer_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dealer_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` bigint unsigned NOT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addr1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addr2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `list_display_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dealer_addresses_dealer_id_foreign` (`dealer_id`),
  KEY `dealer_addresses_type_index` (`type`),
  KEY `dealer_addresses_is_primary_index` (`is_primary`),
  KEY `dealer_addresses_list_display_order_index` (`list_display_order`),
  CONSTRAINT `dealer_addresses_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dealer_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dealer_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` bigint unsigned NOT NULL,
  `document_file_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dealer_files_dealer_id_foreign` (`dealer_id`),
  KEY `dealer_files_document_file_id_foreign` (`document_file_id`),
  CONSTRAINT `dealer_files_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dealer_files_document_file_id_foreign` FOREIGN KEY (`document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dealer_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dealer_terms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dealer_id` bigint unsigned NOT NULL,
  `name` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dealer_terms_dealer_id_foreign` (`dealer_id`),
  CONSTRAINT `dealer_terms_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dealers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dealers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile2` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_id` bigint unsigned NOT NULL,
  `district_id` bigint unsigned NOT NULL,
  `city_id` bigint unsigned NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dealers_state_id_foreign` (`state_id`),
  KEY `dealers_district_id_foreign` (`district_id`),
  KEY `dealers_city_id_foreign` (`city_id`),
  KEY `dealers_type_index` (`type`),
  KEY `dealers_name_index` (`name`),
  KEY `dealers_mobile_index` (`mobile`),
  KEY `dealers_is_active_index` (`is_active`),
  KEY `dealers_list_display_order_index` (`list_display_order`),
  CONSTRAINT `dealers_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dealers_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dealers_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dept_head_user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `list_display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_name_unique` (`name`),
  KEY `departments_dept_head_user_id_foreign` (`dept_head_user_id`),
  KEY `departments_short_name_index` (`short_name`),
  KEY `departments_is_active_index` (`is_active`),
  KEY `departments_list_display_order_index` (`list_display_order`),
  CONSTRAINT `departments_dept_head_user_id_foreign` FOREIGN KEY (`dept_head_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `designations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `designations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `designations_name_unique` (`name`),
  KEY `designations_short_name_index` (`short_name`),
  KEY `designations_is_active_index` (`is_active`),
  KEY `designations_list_display_order_index` (`list_display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dispatch_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispatch_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dispatch_order_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `uom_id` bigint unsigned NOT NULL,
  `qty` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispatch_order_items_dispatch_order_id_foreign` (`dispatch_order_id`),
  KEY `dispatch_order_items_item_id_foreign` (`item_id`),
  KEY `dispatch_order_items_uom_id_foreign` (`uom_id`),
  CONSTRAINT `dispatch_order_items_dispatch_order_id_foreign` FOREIGN KEY (`dispatch_order_id`) REFERENCES `dispatch_orders` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dispatch_order_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dispatch_order_items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `uoms` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dispatch_order_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispatch_order_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dispatch_order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `qty` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispatch_order_products_dispatch_order_id_foreign` (`dispatch_order_id`),
  KEY `dispatch_order_products_product_id_foreign` (`product_id`),
  CONSTRAINT `dispatch_order_products_dispatch_order_id_foreign` FOREIGN KEY (`dispatch_order_id`) REFERENCES `dispatch_orders` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dispatch_order_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dispatch_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispatch_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_id` bigint unsigned DEFAULT NULL,
  `addr1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addr2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` bigint unsigned NOT NULL,
  `state_id` bigint unsigned NOT NULL,
  `district_id` bigint unsigned DEFAULT NULL,
  `city_id` bigint unsigned DEFAULT NULL,
  `docket_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `builty_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_phone_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_fee` double NOT NULL DEFAULT '0',
  `shipping_fee_gst_per` double NOT NULL DEFAULT '0',
  `shipping_fee_gst` double NOT NULL DEFAULT '0',
  `total_fee` double NOT NULL DEFAULT '0',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status_type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  `carrier_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispatch_orders_lead_id_foreign` (`lead_id`),
  KEY `dispatch_orders_country_id_foreign` (`country_id`),
  KEY `dispatch_orders_state_id_foreign` (`state_id`),
  KEY `dispatch_orders_district_id_foreign` (`district_id`),
  KEY `dispatch_orders_city_id_foreign` (`city_id`),
  KEY `dispatch_orders_carrier_id_foreign` (`carrier_id`),
  KEY `dispatch_orders_type_index` (`type`),
  KEY `dispatch_orders_docket_no_index` (`docket_no`),
  KEY `dispatch_orders_builty_no_index` (`builty_no`),
  KEY `dispatch_orders_tracking_no_index` (`tracking_no`),
  KEY `dispatch_orders_status_type_index` (`status_type`),
  CONSTRAINT `dispatch_orders_carrier_id_foreign` FOREIGN KEY (`carrier_id`) REFERENCES `parties` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dispatch_orders_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dispatch_orders_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dispatch_orders_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dispatch_orders_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `dispatch_orders_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `districts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_id` bigint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `districts_state_id_foreign` (`state_id`),
  KEY `districts_name_index` (`name`),
  KEY `districts_is_active_index` (`is_active`),
  KEY `districts_list_display_order_index` (`list_display_order`),
  CONSTRAINT `districts_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `document_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_last_node` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_categories_name_index` (`name`),
  KEY `document_categories_parent_id_index` (`parent_id`),
  KEY `document_categories_is_last_node_index` (`is_last_node`),
  KEY `document_categories_is_active_index` (`is_active`),
  KEY `document_categories_list_display_order_index` (`list_display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `document_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_category_id` bigint unsigned NOT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `storage_base_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_original` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_full_path` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_ext` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size_in_mb` double DEFAULT NULL,
  `image_height` int DEFAULT NULL,
  `image_width` int DEFAULT NULL,
  `video_duration_in_seconds` int DEFAULT NULL,
  `audio_duration_in_seconds` int DEFAULT NULL,
  `is_archive` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_files_document_category_id_foreign` (`document_category_id`),
  CONSTRAINT `document_files_document_category_id_foreign` FOREIGN KEY (`document_category_id`) REFERENCES `document_categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `drop_down_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drop_down_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `drop_down_values_type_index` (`type`),
  KEY `drop_down_values_name_index` (`name`),
  KEY `drop_down_values_short_name_index` (`short_name`),
  KEY `drop_down_values_is_active_index` (`is_active`),
  KEY `drop_down_values_list_display_order_index` (`list_display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `email_template_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_template_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email_template_id` bigint unsigned NOT NULL,
  `document_file_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_template_files_email_template_id_foreign` (`email_template_id`),
  KEY `email_template_files_document_file_id_foreign` (`document_file_id`),
  CONSTRAINT `email_template_files_document_file_id_foreign` FOREIGN KEY (`document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `email_template_files_email_template_id_foreign` FOREIGN KEY (`email_template_id`) REFERENCES `email_templates` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `placeholders` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `holiday_branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `holiday_branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `holiday_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `holiday_branches_holiday_id_foreign` (`holiday_id`),
  KEY `holiday_branches_branch_id_foreign` (`branch_id`),
  CONSTRAINT `holiday_branches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `holiday_branches_holiday_id_foreign` FOREIGN KEY (`holiday_id`) REFERENCES `holidays` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `holiday_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `holiday_dates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `holiday_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `holiday_dates_holiday_id_foreign` (`holiday_id`),
  CONSTRAINT `holiday_dates_holiday_id_foreign` FOREIGN KEY (`holiday_id`) REFERENCES `holidays` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `holidays` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `for_all_branches` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `item_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_uom_id` bigint unsigned NOT NULL,
  `is_last_node` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_categories_base_uom_id_foreign` (`base_uom_id`),
  KEY `item_categories_parent_id_index` (`parent_id`),
  KEY `item_categories_name_index` (`name`),
  KEY `item_categories_short_name_index` (`short_name`),
  KEY `item_categories_is_last_node_index` (`is_last_node`),
  KEY `item_categories_is_active_index` (`is_active`),
  KEY `item_categories_list_display_order_index` (`list_display_order`),
  CONSTRAINT `item_categories_base_uom_id_foreign` FOREIGN KEY (`base_uom_id`) REFERENCES `uoms` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `item_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `file_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_id` bigint unsigned NOT NULL,
  `document_file_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_files_item_id_foreign` (`item_id`),
  KEY `item_files_document_file_id_foreign` (`document_file_id`),
  CONSTRAINT `item_files_document_file_id_foreign` FOREIGN KEY (`document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `item_files_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color_id` bigint unsigned DEFAULT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` bigint unsigned DEFAULT NULL,
  `specification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `main_image_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `items_item_category_id_foreign` (`item_category_id`),
  KEY `items_color_id_foreign` (`color_id`),
  KEY `items_brand_id_foreign` (`brand_id`),
  KEY `items_main_image_id_foreign` (`main_image_id`),
  KEY `items_name_index` (`name`),
  KEY `items_sku_index` (`sku`),
  KEY `items_is_active_index` (`is_active`),
  KEY `items_list_display_order_index` (`list_display_order`),
  CONSTRAINT `items_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `drop_down_values` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `items_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `drop_down_values` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `items_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `items_main_image_id_foreign` FOREIGN KEY (`main_image_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lead_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addr1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addr2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `list_display_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_addresses_lead_id_foreign` (`lead_id`),
  KEY `lead_addresses_type_index` (`type`),
  KEY `lead_addresses_is_primary_index` (`is_primary`),
  KEY `lead_addresses_list_display_order_index` (`list_display_order`),
  CONSTRAINT `lead_addresses_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lead_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `document_file_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_files_lead_id_foreign` (`lead_id`),
  KEY `lead_files_document_file_id_foreign` (`document_file_id`),
  CONSTRAINT `lead_files_document_file_id_foreign` FOREIGN KEY (`document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `lead_files_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lead_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `entry_by` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_type_id` bigint unsigned DEFAULT NULL,
  `response` text COLLATE utf8mb4_unicode_ci,
  `recording_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `next_meeting_date_time` datetime DEFAULT NULL,
  `is_lead_transfered` tinyint(1) NOT NULL DEFAULT '0',
  `assign_to_user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_histories_lead_id_foreign` (`lead_id`),
  KEY `lead_histories_response_type_id_foreign` (`response_type_id`),
  KEY `lead_histories_assign_to_user_id_foreign` (`assign_to_user_id`),
  KEY `lead_histories_status_type_index` (`status_type`),
  KEY `lead_histories_next_meeting_date_time_index` (`next_meeting_date_time`),
  CONSTRAINT `lead_histories_assign_to_user_id_foreign` FOREIGN KEY (`assign_to_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `lead_histories_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `lead_histories_response_type_id_foreign` FOREIGN KEY (`response_type_id`) REFERENCES `drop_down_values` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lead_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `uom_id` bigint unsigned NOT NULL,
  `qty` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_items_lead_id_foreign` (`lead_id`),
  KEY `lead_items_item_id_foreign` (`item_id`),
  KEY `lead_items_uom_id_foreign` (`uom_id`),
  CONSTRAINT `lead_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `lead_items_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `lead_items_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `uoms` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lead_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `qty` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_products_lead_id_foreign` (`lead_id`),
  KEY `lead_products_product_id_foreign` (`product_id`),
  CONSTRAINT `lead_products_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `lead_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `is_customer` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile2` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_gst_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_id` bigint unsigned DEFAULT NULL,
  `state_id` bigint unsigned DEFAULT NULL,
  `district_id` bigint unsigned DEFAULT NULL,
  `city_id` bigint unsigned DEFAULT NULL,
  `deal_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dealer_id` bigint unsigned DEFAULT NULL,
  `campaign_id` bigint unsigned DEFAULT NULL,
  `response_type_id` bigint unsigned DEFAULT NULL,
  `status_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose_price` double DEFAULT NULL,
  `payable_amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `assign_to_user_id` bigint unsigned DEFAULT NULL,
  `next_meeting_date_time` datetime DEFAULT NULL,
  `is_snooze` tinyint(1) NOT NULL DEFAULT '0',
  `snooze_minute` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  `snooze_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leads_source_id_foreign` (`source_id`),
  KEY `leads_state_id_foreign` (`state_id`),
  KEY `leads_district_id_foreign` (`district_id`),
  KEY `leads_city_id_foreign` (`city_id`),
  KEY `leads_dealer_id_foreign` (`dealer_id`),
  KEY `leads_assign_to_user_id_foreign` (`assign_to_user_id`),
  KEY `leads_next_meeting_date_time_index` (`next_meeting_date_time`),
  KEY `leads_status_type_index` (`status_type`),
  KEY `leads_source_type_index` (`source_type`),
  KEY `leads_deal_type_index` (`deal_type`),
  KEY `leads_campaign_id_foreign` (`campaign_id`),
  KEY `leads_response_type_id_foreign` (`response_type_id`),
  KEY `leads_name_index` (`name`),
  KEY `leads_mobile_index` (`mobile`),
  KEY `leads_mobile2_index` (`mobile2`),
  KEY `leads_email_index` (`email`),
  KEY `leads_is_snooze_snooze_datetime_index` (`is_snooze`,`snooze_datetime`),
  CONSTRAINT `leads_assign_to_user_id_foreign` FOREIGN KEY (`assign_to_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `leads_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `leads_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `leads_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `leads_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `leads_response_type_id_foreign` FOREIGN KEY (`response_type_id`) REFERENCES `drop_down_values` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `leads_source_id_foreign` FOREIGN KEY (`source_id`) REFERENCES `drop_down_values` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `leads_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `makers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `makers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `module_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `main_table` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `main_model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `main_table_field` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_id` bigint unsigned NOT NULL,
  `foreign_table` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foreign_model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foreign_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_links_foreign_model_foreign_table_foreign_id_index` (`foreign_model`,`foreign_table`,`foreign_id`),
  KEY `main_index` (`main_model`,`main_table`,`main_table_field`,`main_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `from_user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `parties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile2` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_id` bigint unsigned DEFAULT NULL,
  `district_id` bigint unsigned DEFAULT NULL,
  `city_id` bigint unsigned DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parties_state_id_foreign` (`state_id`),
  KEY `parties_district_id_foreign` (`district_id`),
  KEY `parties_city_id_foreign` (`city_id`),
  KEY `parties_type_index` (`type`),
  KEY `parties_name_index` (`name`),
  KEY `parties_mobile_index` (`mobile`),
  KEY `parties_is_active_index` (`is_active`),
  KEY `parties_list_display_order_index` (`list_display_order`),
  CONSTRAINT `parties_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `parties_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `parties_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `party_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `party_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `party_id` bigint unsigned NOT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addr1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addr2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `list_display_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `party_addresses_party_id_foreign` (`party_id`),
  KEY `party_addresses_type_index` (`type`),
  KEY `party_addresses_is_primary_index` (`is_primary`),
  KEY `party_addresses_list_display_order_index` (`list_display_order`),
  CONSTRAINT `party_addresses_party_id_foreign` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `party_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `party_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `party_id` bigint unsigned NOT NULL,
  `document_file_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `party_files_party_id_foreign` (`party_id`),
  KEY `party_files_document_file_id_foreign` (`document_file_id`),
  CONSTRAINT `party_files_document_file_id_foreign` FOREIGN KEY (`document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `party_files_party_id_foreign` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `party_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `party_terms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `party_id` bigint unsigned NOT NULL,
  `name` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `party_terms_party_id_foreign` (`party_id`),
  CONSTRAINT `party_terms_party_id_foreign` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `product_category_attribute_id` bigint unsigned NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_attributes_product_id_foreign` (`product_id`),
  KEY `product_attributes_product_category_attribute_id_foreign` (`product_category_attribute_id`),
  CONSTRAINT `product_attributes_product_category_attribute_id_foreign` FOREIGN KEY (`product_category_attribute_id`) REFERENCES `product_category_attributes` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `product_attributes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_last_node` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_categories_parent_id_index` (`parent_id`),
  KEY `product_categories_name_index` (`name`),
  KEY `product_categories_short_name_index` (`short_name`),
  KEY `product_categories_is_last_node_index` (`is_last_node`),
  KEY `product_categories_is_active_index` (`is_active`),
  KEY `product_categories_list_display_order_index` (`list_display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_category_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_category_attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dropdown_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uom_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_category_attributes_product_category_id_foreign` (`product_category_id`),
  KEY `product_category_attributes_uom_id_foreign` (`uom_id`),
  KEY `product_category_attributes_is_active_index` (`is_active`),
  KEY `product_category_attributes_list_display_order_index` (`list_display_order`),
  CONSTRAINT `product_category_attributes_product_category_id_foreign` FOREIGN KEY (`product_category_id`) REFERENCES `product_categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `product_category_attributes_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `uoms` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `file_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint unsigned NOT NULL,
  `document_file_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_files_product_id_foreign` (`product_id`),
  KEY `product_files_document_file_id_foreign` (`document_file_id`),
  CONSTRAINT `product_files_document_file_id_foreign` FOREIGN KEY (`document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `product_files_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_keywords` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_keywords_product_id_foreign` (`product_id`),
  CONSTRAINT `product_keywords_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_serials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_serials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned NOT NULL DEFAULT '0',
  `serial_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `make_id` bigint unsigned NOT NULL,
  `dealer_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `is_parent_serial_no` tinyint(1) NOT NULL DEFAULT '0',
  `is_in` tinyint(1) NOT NULL DEFAULT '0',
  `is_sold` tinyint(1) NOT NULL DEFAULT '0',
  `is_return` tinyint(1) NOT NULL DEFAULT '0',
  `return_count` int NOT NULL DEFAULT '0',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_serials_product_id_foreign` (`product_id`),
  KEY `product_serials_make_id_foreign` (`make_id`),
  KEY `product_serials_dealer_id_foreign` (`dealer_id`),
  KEY `product_serials_customer_id_foreign` (`customer_id`),
  KEY `product_serials_branch_id_foreign` (`branch_id`),
  CONSTRAINT `product_serials_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `product_serials_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `product_serials_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `product_serials_make_id_foreign` FOREIGN KEY (`make_id`) REFERENCES `makers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `product_serials_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_specifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_specifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `group_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_specifications_product_id_foreign` (`product_id`),
  KEY `product_specifications_is_active_index` (`is_active`),
  KEY `product_specifications_list_display_order_index` (`list_display_order`),
  CONSTRAINT `product_specifications_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_category_id` bigint unsigned NOT NULL,
  `catalog_document_file_id` bigint unsigned DEFAULT NULL,
  `installation_guide_document_file_id` bigint unsigned DEFAULT NULL,
  `user_manual_document_file_id` bigint unsigned DEFAULT NULL,
  `troubleshoot_document_file_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `description_html` longtext COLLATE utf8mb4_unicode_ci,
  `length` double DEFAULT NULL,
  `width` double DEFAULT NULL,
  `height` double DEFAULT NULL,
  `gross_length` double DEFAULT '0',
  `gross_width` double DEFAULT '0',
  `gross_height` double DEFAULT '0',
  `size_uom_id` bigint unsigned DEFAULT NULL,
  `net_weight` double NOT NULL DEFAULT '0',
  `gross_weight` double NOT NULL DEFAULT '0',
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `seo_title` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_main_keyword` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `main_image_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL,
  `tab_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_product_category_id_foreign` (`product_category_id`),
  KEY `products_main_image_id_foreign` (`main_image_id`),
  KEY `products_size_uom_id_foreign` (`size_uom_id`),
  KEY `products_name_index` (`name`),
  KEY `products_model_name_index` (`model_name`),
  KEY `products_sku_index` (`sku`),
  KEY `products_is_active_index` (`is_active`),
  KEY `products_list_display_order_index` (`list_display_order`),
  KEY `products_catalog_document_file_id_foreign` (`catalog_document_file_id`),
  KEY `products_installation_guide_document_file_id_foreign` (`installation_guide_document_file_id`),
  KEY `products_user_manual_document_file_id_foreign` (`user_manual_document_file_id`),
  KEY `products_troubleshoot_document_file_id_foreign` (`troubleshoot_document_file_id`),
  CONSTRAINT `products_catalog_document_file_id_foreign` FOREIGN KEY (`catalog_document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `products_installation_guide_document_file_id_foreign` FOREIGN KEY (`installation_guide_document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `products_main_image_id_foreign` FOREIGN KEY (`main_image_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `products_product_category_id_foreign` FOREIGN KEY (`product_category_id`) REFERENCES `product_categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `products_size_uom_id_foreign` FOREIGN KEY (`size_uom_id`) REFERENCES `uoms` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `products_troubleshoot_document_file_id_foreign` FOREIGN KEY (`troubleshoot_document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `products_user_manual_document_file_id_foreign` FOREIGN KEY (`user_manual_document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `recurring_task_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recurring_task_dates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `recurring_task_id` bigint unsigned NOT NULL,
  `date` timestamp NOT NULL,
  `task_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recurring_task_dates_recurring_task_id_foreign` (`recurring_task_id`),
  KEY `recurring_task_dates_task_id_foreign` (`task_id`),
  CONSTRAINT `recurring_task_dates_recurring_task_id_foreign` FOREIGN KEY (`recurring_task_id`) REFERENCES `recurring_tasks` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `recurring_task_dates_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `recurring_task_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recurring_task_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `recurring_task_id` bigint unsigned NOT NULL,
  `document_file_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recurring_task_files_recurring_task_id_foreign` (`recurring_task_id`),
  KEY `recurring_task_files_document_file_id_foreign` (`document_file_id`),
  CONSTRAINT `recurring_task_files_document_file_id_foreign` FOREIGN KEY (`document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `recurring_task_files_recurring_task_id_foreign` FOREIGN KEY (`recurring_task_id`) REFERENCES `recurring_tasks` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `recurring_task_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recurring_task_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `recurring_task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recurring_task_users_recurring_task_id_foreign` (`recurring_task_id`),
  KEY `recurring_task_users_user_id_foreign` (`user_id`),
  CONSTRAINT `recurring_task_users_recurring_task_id_foreign` FOREIGN KEY (`recurring_task_id`) REFERENCES `recurring_tasks` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `recurring_task_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `recurring_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recurring_tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dept_id` bigint unsigned DEFAULT NULL,
  `task_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recurring_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assign_to_user_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `priority` int NOT NULL DEFAULT '1',
  `start_date_time` timestamp NULL DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `duration_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_draft` tinyint(1) NOT NULL DEFAULT '0',
  `is_send_notification` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recurring_tasks_assign_to_user_id_foreign` (`assign_to_user_id`),
  KEY `recurring_tasks_dept_id_foreign` (`dept_id`),
  KEY `recurring_tasks_start_date_time_index` (`start_date_time`),
  KEY `recurring_tasks_is_draft_index` (`is_draft`),
  KEY `recurring_tasks_is_active_index` (`is_active`),
  CONSTRAINT `recurring_tasks_assign_to_user_id_foreign` FOREIGN KEY (`assign_to_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `recurring_tasks_dept_id_foreign` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_route_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_route_names` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `route_name_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_route_names_role_id_foreign` (`role_id`),
  KEY `role_route_names_route_name_id_foreign` (`route_name_id`),
  CONSTRAINT `role_route_names_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_route_names_route_name_id_foreign` FOREIGN KEY (`route_name_id`) REFERENCES `route_names` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_pre_defined` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `route_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `route_names` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `route_names_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sale_order_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_order_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_order_id` bigint unsigned NOT NULL,
  `document_file_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_order_files_sale_order_id_foreign` (`sale_order_id`),
  KEY `sale_order_files_document_file_id_foreign` (`document_file_id`),
  CONSTRAINT `sale_order_files_document_file_id_foreign` FOREIGN KEY (`document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sale_order_files_sale_order_id_foreign` FOREIGN KEY (`sale_order_id`) REFERENCES `sale_orders` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sale_order_product_serials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_order_product_serials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_order_product_id` bigint unsigned NOT NULL,
  `product_serial_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_order_product_serials_sale_order_product_id_foreign` (`sale_order_product_id`),
  KEY `sale_order_product_serials_product_serial_id_foreign` (`product_serial_id`),
  CONSTRAINT `sale_order_product_serials_product_serial_id_foreign` FOREIGN KEY (`product_serial_id`) REFERENCES `product_serials` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sale_order_product_serials_sale_order_product_id_foreign` FOREIGN KEY (`sale_order_product_id`) REFERENCES `sale_order_products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sale_order_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_order_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `make_id` bigint unsigned NOT NULL,
  `order_qty` double NOT NULL,
  `available_qty` double NOT NULL DEFAULT '0',
  `qty_to_manufacture` double NOT NULL DEFAULT '0',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_order_products_sale_order_id_foreign` (`sale_order_id`),
  KEY `sale_order_products_product_id_foreign` (`product_id`),
  KEY `sale_order_products_make_id_foreign` (`make_id`),
  CONSTRAINT `sale_order_products_make_id_foreign` FOREIGN KEY (`make_id`) REFERENCES `makers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sale_order_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sale_order_products_sale_order_id_foreign` FOREIGN KEY (`sale_order_id`) REFERENCES `sale_orders` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sale_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `dealer_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `status` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_dispatch_date` date DEFAULT NULL,
  `dispatch_date_time` datetime DEFAULT NULL,
  `carrier_id` bigint unsigned DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sale_orders_order_no_unique` (`order_no`),
  KEY `sale_orders_branch_id_foreign` (`branch_id`),
  KEY `sale_orders_dealer_id_foreign` (`dealer_id`),
  KEY `sale_orders_customer_id_foreign` (`customer_id`),
  KEY `sale_orders_carrier_id_foreign` (`carrier_id`),
  CONSTRAINT `sale_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sale_orders_carrier_id_foreign` FOREIGN KEY (`carrier_id`) REFERENCES `parties` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sale_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sale_orders_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `states` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` bigint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `states_name_index` (`name`),
  KEY `states_short_name_index` (`short_name`),
  KEY `states_country_id_index` (`country_id`),
  KEY `states_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sub_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint unsigned NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sub_departments_name_unique` (`name`),
  KEY `sub_departments_department_id_foreign` (`department_id`),
  CONSTRAINT `sub_departments_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `main_model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_table` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_table_field` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `search_term` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foreign_table` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foreign_model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foreign_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tags_main_model_main_table_main_id_index` (`main_model`,`main_table`,`main_id`),
  KEY `tags_foreign_model_foreign_table_foreign_id_index` (`foreign_model`,`foreign_table`,`foreign_id`),
  KEY `tags_search_term_index` (`search_term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `lead_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_customers_task_id_foreign` (`task_id`),
  KEY `task_customers_lead_id_foreign` (`lead_id`),
  CONSTRAINT `task_customers_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_customers_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_dealers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_dealers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `dealer_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_dealers_task_id_foreign` (`task_id`),
  KEY `task_dealers_dealer_id_foreign` (`dealer_id`),
  CONSTRAINT `task_dealers_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_dealers_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `document_file_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_files_task_id_foreign` (`task_id`),
  KEY `task_files_document_file_id_foreign` (`document_file_id`),
  CONSTRAINT `task_files_document_file_id_foreign` FOREIGN KEY (`document_file_id`) REFERENCES `document_files` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `task_files_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_leads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `lead_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_leads_task_id_foreign` (`task_id`),
  KEY `task_leads_lead_id_foreign` (`lead_id`),
  CONSTRAINT `task_leads_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_leads_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_link_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_link_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_link_users_task_id_foreign` (`task_id`),
  KEY `task_link_users_user_id_foreign` (`user_id`),
  CONSTRAINT `task_link_users_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `task_link_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_parties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_parties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `party_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_parties_task_id_foreign` (`task_id`),
  KEY `task_parties_party_id_foreign` (`party_id`),
  CONSTRAINT `task_parties_party_id_foreign` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_parties_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_taken_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_taken_actions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `action_taken_id` bigint unsigned NOT NULL,
  `response_taken_id` bigint unsigned NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_taken_actions_task_id_foreign` (`task_id`),
  KEY `task_taken_actions_action_taken_id_foreign` (`action_taken_id`),
  KEY `task_taken_actions_response_taken_id_foreign` (`response_taken_id`),
  CONSTRAINT `task_taken_actions_action_taken_id_foreign` FOREIGN KEY (`action_taken_id`) REFERENCES `drop_down_values` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `task_taken_actions_response_taken_id_foreign` FOREIGN KEY (`response_taken_id`) REFERENCES `drop_down_values` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `task_taken_actions_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dept_id` bigint unsigned DEFAULT NULL,
  `recurring_task_id` bigint unsigned DEFAULT NULL,
  `task_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assign_to_user_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date_time` timestamp NULL DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `duration_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_date_time` timestamp NULL DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '1',
  `contact_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_draft` tinyint(1) NOT NULL DEFAULT '0',
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `complete_date_time` datetime DEFAULT NULL,
  `complete_by` bigint unsigned DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_date_time` datetime DEFAULT NULL,
  `verified_by` bigint unsigned DEFAULT NULL,
  `is_send_notification` tinyint(1) NOT NULL DEFAULT '0',
  `rating` double DEFAULT NULL,
  `review_remarks` text COLLATE utf8mb4_unicode_ci,
  `can_delete` tinyint(1) NOT NULL DEFAULT '1',
  `cannot_delete_reason_array` text COLLATE utf8mb4_unicode_ci,
  `can_edit` tinyint(1) NOT NULL DEFAULT '1',
  `cannot_edit_reason_array` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_assign_to_user_id_foreign` (`assign_to_user_id`),
  KEY `tasks_recurring_task_id_foreign` (`recurring_task_id`),
  KEY `tasks_dept_id_foreign` (`dept_id`),
  KEY `tasks_start_date_time_index` (`start_date_time`),
  KEY `tasks_end_date_time_index` (`end_date_time`),
  KEY `tasks_status_index` (`status`),
  KEY `tasks_is_draft_index` (`is_draft`),
  KEY `tasks_is_completed_index` (`is_completed`),
  KEY `tasks_is_verified_index` (`is_verified`),
  CONSTRAINT `tasks_dept_id_foreign` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `tasks_recurring_task_id_foreign` FOREIGN KEY (`recurring_task_id`) REFERENCES `recurring_tasks` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries` (
  `sequence` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sequence`),
  UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  KEY `telescope_entries_batch_id_index` (`batch_id`),
  KEY `telescope_entries_family_hash_index` (`family_hash`),
  KEY `telescope_entries_created_at_index` (`created_at`),
  KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`entry_uuid`,`tag`),
  KEY `telescope_entries_tags_tag_index` (`tag`),
  CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_monitoring` (
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `types_code_index` (`code`),
  KEY `types_type_index` (`type`),
  KEY `types_name_index` (`name`),
  KEY `types_is_active_index` (`is_active`),
  KEY `types_list_display_order_index` (`list_display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `uoms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `uoms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversion_factor` double DEFAULT NULL,
  `is_last_node` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `list_display_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uoms_code_index` (`code`),
  KEY `uoms_parent_id_index` (`parent_id`),
  KEY `uoms_name_index` (`name`),
  KEY `uoms_is_last_node_index` (`is_last_node`),
  KEY `uoms_is_active_index` (`is_active`),
  KEY `uoms_list_display_order_index` (`list_display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_allow_ips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_allow_ips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_allow_ips_user_id_foreign` (`user_id`),
  CONSTRAINT `user_allow_ips_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_pre_defined` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_roles_role_id_foreign` (`role_id`),
  KEY `user_roles_user_id_foreign` (`user_id`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_category_id` bigint unsigned DEFAULT NULL,
  `dept_id` bigint unsigned DEFAULT NULL,
  `sub_dept_id` bigint unsigned DEFAULT NULL,
  `desg_id` bigint unsigned DEFAULT NULL,
  `country_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `person_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person_last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voip_mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_landline_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_sent_datetime` datetime DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plain_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_via` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apple_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dont_send_email` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_secure_login` tinyint(1) NOT NULL DEFAULT '0',
  `login_timezone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_pre_defined` tinyint(1) NOT NULL DEFAULT '0',
  `can_edit` tinyint(1) NOT NULL DEFAULT '1',
  `cannot_edit_reason_array` json DEFAULT NULL,
  `can_delete` tinyint(1) NOT NULL DEFAULT '1',
  `cannot_delete_reason_array` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_user_category_id_foreign` (`user_category_id`),
  KEY `users_country_id_foreign` (`country_id`),
  KEY `users_dept_id_foreign` (`dept_id`),
  KEY `users_sub_dept_id_foreign` (`sub_dept_id`),
  KEY `users_desg_id_foreign` (`desg_id`),
  KEY `users_name_index` (`name`),
  KEY `users_person_name_index` (`person_name`),
  KEY `users_person_last_name_index` (`person_last_name`),
  KEY `users_sub_type_index` (`sub_type`),
  KEY `users_mobile_index` (`mobile`),
  KEY `users_whatsapp_mobile_index` (`whatsapp_mobile`),
  KEY `users_branch_id_foreign` (`branch_id`),
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `users_dept_id_foreign` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `users_desg_id_foreign` FOREIGN KEY (`desg_id`) REFERENCES `designations` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `users_sub_dept_id_foreign` FOREIGN KEY (`sub_dept_id`) REFERENCES `sub_departments` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `whatsapp_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `whatsapp_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_header_values` text COLLATE utf8mb4_unicode_ci,
  `default_body_values` text COLLATE utf8mb4_unicode_ci,
  `default_button_values` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `updated_by` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `whatsapp_templates_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2023_09_07_040944_create_roles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2023_09_07_060642_create_route_names_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2023_09_07_101921_create_role_route_names_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2023_09_07_103640_create_user_roles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2023_09_16_045002_create_sql_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2023_10_23_040008_add_profile_image_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2023_11_30_113454_create_email_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2024_02_01_072504_create_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2024_02_14_090414_create_table_import_logs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2024_08_28_053206_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_02_11_110103_alter_users_11feb2025',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_02_11_113638_alter_users_11feb2025_a',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_03_14_112316_create_country_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_03_14_112318_create_states_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_03_14_112320_create_departments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_03_14_112520_create_designations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_03_15_034247_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_06_26_112317_insert_country_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_06_26_112319_insert_state_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_06_26_181416_alter_users_add_mobile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_06_27_074726_add_users_plain_password',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_06_27_075301_create_table_user_allow_ips',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_06_27_103014_create_user_catgories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_06_27_103042_add_users_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_06_27_120521_add_users_fields_27june2025',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_06_28_061838_alter_country',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_06_28_164452_alter_users_remove_mobile_unique',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_06_30_040412_create_tasks',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_06_30_041429_create_task_recurring_dates',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_06_30_043705_alter_types',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_06_30_061351_alter_types_30june2025',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2025_06_30_083755_alter_tasks_30june2025',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2025_07_01_050008_alter_tasks_01july2025',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2025_07_01_093650_create_app_menus',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2025_07_02_094416_create_app_menu_related_links',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2025_07_02_115242_create_sub_departments',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2025_07_02_125522_alter_designations',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2025_07_03_112142_alter_app_menus_07july2025',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2025_07_05_035009_create_recurring_tasks',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2025_07_05_035220_create_recurring_task_dates',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2025_07_05_082550_create_tasks_again',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2025_07_06_035449_create_telescope_entries_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2025_07_06_050921_create_notifications_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2025_07_06_071557_create_tags',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2025_07_06_091448_create_module_links',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2025_07_07_054522_alter_users_add_fields',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2025_07_07_061414_alter_tasks_add_fields',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2025_07_07_075432_alter_recurring_tasks_add_fields',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2025_07_08_043458_alter_departments',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2025_07_08_082206_alter_tasks_08july2025',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2025_07_09_050745_alter_users_09july2025',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2025_07_09_104323_create_product_categories',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2025_07_09_104328_create_products',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2025_07_09_164328_alter_notifications',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2025_07_09_173401_alter_notifications_09july',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2025_07_10_035736_alter_module_links_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2025_07_10_120527_alter_products_10july',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2025_07_11_060415_create_product_category_attributes',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2025_07_11_071250_create_product_attributes',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2025_07_17_051822_alter_recurring_tasks',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2025_07_17_060352_create_task_link_users',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2025_07_17_071121_dropdown_values',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (114,'2025_07_17_071250_task_taken_actions',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2025_07_17_110000_create_document_categories',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2025_07_17_113855_create_document_files_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2025_07_19_052030_alter_document_files',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2025_07_21_064552_create_task_files',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (127,'2025_07_22_062100_create_uoms',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (128,'2025_07_22_062126_alter_product_category_attributes',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (130,'2025_07_22_082348_alter_tasks_22july',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (132,'2025_07_22_101927_create_recurring_task_users',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (133,'2025_07_22_103554_create_recurring_task_files',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (134,'2025_07_22_113238_alter_departments',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (135,'2025_07_22_113251_alter_sub_departments',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (136,'2025_07_22_113303_alter_designations',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (137,'2025_07_24_070751_alter_users_24july',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (138,'2025_07_25_121008_alter_users_25july',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (139,'2025_07_26_055933_alter_users_26july',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (140,'2025_07_28_053131_create_whatsapp_templates',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (145,'2025_07_28_073221_create_email_templates',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (147,'2025_07_28_073428_create_email_template_document_files',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (149,'2025_07_29_094410_create_product_attributes',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (151,'2025_07_29_111756_create_product_files',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (152,'2025_07_29_112001_alter_products',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (153,'2025_07_30_044105_alter_whatsapp_templates',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (154,'2025_07_30_120036_alter_users_30july',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (167,'2025_08_02_090931_alter_products_02aug',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (168,'2025_08_02_092817_create_product_keywords',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (169,'2025_08_02_093108_alter_product_files',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (170,'2025_08_02_093252_create_product_specifications',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (171,'2025_08_02_105521_create_product_catalogs',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (172,'2025_08_04_035516_alter_uoms_04aug',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (173,'2025_08_04_040446_alter_products_04aug',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (174,'2025_08_04_093150_create_parties',35);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (175,'2025_08_04_120331_create_task_parties',36);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (176,'2025_08_05_055919_alter_products_05aug',37);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (177,'2025_08_06_054837_create_districts',38);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (178,'2025_08_06_054838_create_city_table',38);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (183,'2025_08_07_085852_create_party_terms',39);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (184,'2025_08_07_090344_create_party_files',39);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (185,'2025_08_07_105940_create_dealers',40);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (186,'2025_08_07_105941_create_leads',40);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (187,'2025_08_07_113702_create_dealer_terms',41);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (188,'2025_08_07_113707_create_dealer_files',41);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (190,'2025_08_07_122110_create_dealer_addresses',42);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (191,'2025_08_08_042835_create_lead_addresses',43);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (192,'2025_08_08_042857_create_lead_products',43);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (194,'2025_08_08_045842_alter_leads_08aug',44);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (195,'2025_08_08_113707_create_lead_files',45);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (207,'2025_08_08_074359_create_lead_histories',46);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (208,'2025_08_08_075417_alter_leads_08aug_a',46);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (209,'2025_08_08_123445_alter_users_08aug',46);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (210,'2025_08_09_103735_alter_leads_09aug',47);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (218,'2025_08_11_043232_create_item_categories',48);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (225,'2025_08_11_043249_create_items',49);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (226,'2025_08_11_044454_create_item_files',49);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (228,'2025_08_12_063456_create_lead_items',50);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (230,'2025_08_12_075509_create_carriers',51);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (231,'2025_08_12_075726_create_carrier_terms',52);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (232,'2025_08_12_075734_create_carrier_files',52);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (252,'2025_08_13_041345_create_dispatch_orders',53);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (253,'2025_08_13_041355_create_dispatch_order_items',53);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (254,'2025_08_13_041400_create_dispatch_order_products',53);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (256,'2025_08_13_054116_alter_leads_13aug',54);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (257,'2025_08_13_094043_create_task_leads',55);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (258,'2025_08_13_094049_create_task_customers',55);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (259,'2025_08_13_094056_create_task_dealers',55);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (260,'2025_08_13_094548_alter_task_13aug',56);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (265,'2025_08_13_103739_create_campaigns',57);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (266,'2025_08_13_104239_create_campaign_files',57);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (267,'2025_08_13_110353_alter_leads_13aug_a',58);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (268,'2025_08_14_094521_create_party_addresses',59);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (269,'2025_08_14_095234_alter_party_14aug',60);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (270,'2025_08_14_095311_create_companies',60);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (271,'2025_08_14_095317_create_branches',61);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (272,'2025_08_14_095332_create_holidays',61);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (273,'2025_08_14_095342_create_holiday_dates',61);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (274,'2025_08_14_095359_create_holiday_branches',61);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (275,'2025_08_14_100614_alter_dealer_14aug',61);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (276,'2025_08_14_102427_alter_party_14aug_a',62);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (277,'2025_08_14_102914_alter_carrier_14aug',63);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (278,'2025_08_14_114948_alter_carrier_14aug_a',64);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (279,'2025_08_14_114955_alter_dealer_14aug_a',64);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (280,'2025_08_14_115001_alter_party_14aug_a',64);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (281,'2025_08_16_042043_alter_holidays_16aug',65);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (284,'2025_08_16_064622_alter_lead_16aug',66);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (285,'2025_08_16_114422_alter_branches_16aug',67);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (286,'2025_08_19_050723_alter_for_change_carrier_id',68);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (287,'2025_08_19_053131_alter_lead_19aug',68);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (288,'2025_08_19_054846_alter_lead_add_indexes',69);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (289,'2025_08_19_055509_alter_branch_index',70);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (290,'2025_08_19_060510_alter_lead_19aug_a',71);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (291,'2025_08_19_081144_drop_tables',72);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (292,'2025_08_19_081255_alter_indexes',72);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (293,'2025_08_21_101331_alter_products_21aug',73);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (294,'2025_08_21_104439_alter_products_21aug_a',74);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (295,'2025_08_21_112702_create_customers',75);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (296,'2025_08_21_112720_create_makers',75);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (301,'2025_08_21_112737_create_product_serials',76);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (302,'2025_08_21_114109_create_auto_serial_numbers',76);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (303,'2025_08_22_050256_alter_users_22aug',77);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (304,'2025_08_22_074354_alter_products_22aug',78);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (305,'2025_08_22_105642_alter_products_22aug_a',79);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (307,'2025_08_23_064920_alter_product_serial_23aug',80);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (308,'2025_08_23_082210_alter_product_serial_23aug_a',81);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (309,'2025_08_23_094711_alter_product_serial_23aug_b',82);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (315,'2025_08_23_103900_create_sale_orders',83);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (316,'2025_08_23_103909_create_sale_order_products',83);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (317,'2025_08_23_105303_create_sale_order_product_serials',83);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (318,'2025_08_23_110152_alter_customers',83);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (319,'2025_08_23_110840_create_sale_order_files',83);
