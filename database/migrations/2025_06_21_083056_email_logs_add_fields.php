<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->boolean("is_sandbox")->default(0)->after("content_file");
            $table->text("cc_name")->nullable()->after("to_email");
            $table->text("cc_email")->nullable()->after("cc_name");
            $table->text("api_response")->nullable()->after("is_sent");
            $table->string("api_response_code", 255)->nullable()->after("api_response");
            $table->text("error_msg")->nullable()->after("api_response");
            $table->unsignedBigInteger("updated_by")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn('is_sandbox');
            $table->dropColumn('cc_name');
            $table->dropColumn('cc_email');
            $table->dropColumn('api_response');
            $table->dropColumn('api_response_code');
            $table->dropColumn('error_msg');
            $table->dropColumn('updated_by');
        });
    }
};
