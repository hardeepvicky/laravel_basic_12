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
        Schema::table('notifications', function (Blueprint $table) {
            $table->string("sub_type")->nullable()->after("type");
            $table->unsignedBigInteger("from_user_id")->nullable()->after("read_at");
            $table->unsignedBigInteger("created_by")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropColumnsWithIndex("notifications", ["sub_type", "from_user_id", "created_by"]);
    }
};
