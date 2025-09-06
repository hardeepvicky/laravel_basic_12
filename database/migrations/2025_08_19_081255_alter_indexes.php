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
        $this->dropIndexIfExist("campaigns", ["name", "start_date", "end_date", "is_active", "list_display_order"]);

        Schema::table('campaigns', function (Blueprint $table) {
            $table->index(["name"]);
            $table->index(["start_date"]);
            $table->index(["end_date"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        $this->dropIndexIfExist("cities", ["name", "is_active", "list_display_order"]);

        Schema::table('cities', function (Blueprint $table) {
            $table->index(["name"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        $this->dropIndexIfExist("countries", ["name", "short_name", "is_active"]);

        Schema::table('countries', function (Blueprint $table) {
            $table->index(["name"]);
            $table->index(["short_name"]);
            $table->index(["is_active"]);
        });

        $this->dropIndexIfExist("dealer_addresses", ["type", "is_primary", "list_display_order"]);

        Schema::table('dealer_addresses', function (Blueprint $table) {
            $table->index(["type"]);
            $table->index(["is_primary"]);
            $table->index(["list_display_order"]);
        });

        $this->dropIndexIfExist("dealers", ["type", "name", "mobile", "is_active", "list_display_order"]);

        Schema::table('dealers', function (Blueprint $table) {
            $table->index(["type"]);
            $table->index(["name"]);
            $table->index(["mobile"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        $this->dropIndexIfExist("departments", ["short_name", "is_active", "list_display_order"]);

        Schema::table('departments', function (Blueprint $table) {
            $table->index(["short_name"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        $this->dropIndexIfExist("designations", ["short_name", "is_active", "list_display_order"]);

        Schema::table('designations', function (Blueprint $table) {
            $table->index(["short_name"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        $this->dropIndexIfExist("dispatch_orders", ["type", "docket_no", "builty_no", "tracking_no", "status_type"]);

        Schema::table('dispatch_orders', function (Blueprint $table) {
            $table->index(["type"]);
            $table->index(["docket_no"]);
            $table->index(["builty_no"]);
            $table->index(["tracking_no"]);
            $table->index(["status_type"]);
        });

        $this->dropIndexIfExist("districts", ["name", "is_active", "list_display_order"]);

        Schema::table('districts', function (Blueprint $table) {
            $table->index(["name"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        $this->dropIndexIfExist("document_categories", ["name", "parent_id", "is_last_node", "is_active", "list_display_order"]);

        Schema::table('document_categories', function (Blueprint $table) {
            $table->index(["name"]);
            $table->index(["parent_id"]);
            $table->index(["is_last_node"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        $this->dropIndexIfExist("drop_down_values", ["type", "name", "short_name", "is_active", "list_display_order"]);

        Schema::table('drop_down_values', function (Blueprint $table) {
            $table->index(["type"]);
            $table->index(["name"]);
            $table->index(["short_name"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        $this->dropIndexIfExist("item_categories", ["parent_id", "name", "short_name", "is_last_node", "is_active", "list_display_order"]);

        Schema::table('item_categories', function (Blueprint $table) {
            $table->index(["parent_id"]);
            $table->index(["name"]);
            $table->index(["short_name"]);
            $table->index(["is_last_node"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        $this->dropIndexIfExist("items", ["name", "sku", "is_active", "list_display_order"]);

        Schema::table('items', function (Blueprint $table) {
            $table->index(["name"]);
            $table->index(["sku"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        
        $this->dropIndexIfExist("lead_addresses", ["type", "is_primary", "list_display_order"]);

        Schema::table('lead_addresses', function (Blueprint $table) {
            $table->index(["type"]);
            $table->index(["is_primary"]);
            $table->index(["list_display_order"]);
        });
        
        $this->dropIndexIfExist("parties", ["type", "name", "mobile", "is_active", "list_display_order"]);

        Schema::table('parties', function (Blueprint $table) {
            $table->index(["type"]);
            $table->index(["name"]);
            $table->index(["mobile"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        $this->dropIndexIfExist("party_addresses", ["type", "is_primary", "list_display_order"]);

        Schema::table('party_addresses', function (Blueprint $table) {
            $table->index(["type"]);
            $table->index(["is_primary"]);
            $table->index(["list_display_order"]);
        });
        
        $this->dropIndexIfExist("product_categories", ["parent_id", "name", "short_name", "is_last_node", "is_active", "list_display_order"]);

        Schema::table('product_categories', function (Blueprint $table) {
            $table->index(["parent_id"]);
            $table->index(["name"]);
            $table->index(["short_name"]);
            $table->index(["is_last_node"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });
        
        $this->dropIndexIfExist("product_category_attributes", ["is_active", "list_display_order"]);

        Schema::table('product_category_attributes', function (Blueprint $table) {
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });
        
        $this->dropIndexIfExist("product_specifications", ["is_active", "list_display_order"]);

        Schema::table('product_specifications', function (Blueprint $table) {
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });

        
        $this->dropIndexIfExist("products", ["name", "model_name", "sku", "is_active", "list_display_order"]);

        Schema::table('products', function (Blueprint $table) {
            $table->index(["name"]);
            $table->index(["model_name"]);
            $table->index(["sku"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });
        
        $this->dropIndexIfExist("recurring_tasks", ["start_date_time", "is_draft", "is_active"]);

        Schema::table('recurring_tasks', function (Blueprint $table) {
            $table->index(["start_date_time"]);
            $table->index(["is_draft"]);
            $table->index(["is_active"]);
        });
        
        $this->dropIndexIfExist("states", ["name", "short_name", "country_id", "is_active"]);

        Schema::table('states', function (Blueprint $table) {
            $table->index(["name"]);
            $table->index(["short_name"]);
            $table->index(["country_id"]);
            $table->index(["is_active"]);
        });

        
        $this->dropIndexIfExist("tasks", ["start_date_time", "end_date_time", "status", "is_draft", "is_completed", "is_verified"]);

        Schema::table('tasks', function (Blueprint $table) {
            $table->index(["start_date_time"]);
            $table->index(["end_date_time"]);
            $table->index(["status"]);
            $table->index(["is_draft"]);
            $table->index(["is_completed"]);
            $table->index(["is_verified"]);
        });
        
        $this->dropIndexIfExist("types", ["code", "type", "name", "is_active", "list_display_order"]);

        Schema::table('types', function (Blueprint $table) {
            $table->index(["code"]);
            $table->index(["type"]);
            $table->index(["name"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });
        
        $this->dropIndexIfExist("uoms", ["code", "parent_id", "name", "is_last_node", "is_active", "list_display_order"]);

        Schema::table('uoms', function (Blueprint $table) {
            $table->index(["code"]);
            $table->index(["parent_id"]);
            $table->index(["name"]);
            $table->index(["is_last_node"]);
            $table->index(["is_active"]);
            $table->index(["list_display_order"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
