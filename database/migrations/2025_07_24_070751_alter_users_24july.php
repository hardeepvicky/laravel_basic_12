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
        Schema::table('users', function (Blueprint $table) {
            $table->string("person_last_name")->nullable()->after("person_name");
            $table->string("sub_type")->nullable()->after("type");

            $table->index("name");
            $table->index("person_name");
            $table->index("person_last_name");
            $table->index("sub_type");
            $table->index("mobile");
            $table->index("whatsapp_mobile");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("users", ["person_last_name", "sub_type"]);
    }
};
