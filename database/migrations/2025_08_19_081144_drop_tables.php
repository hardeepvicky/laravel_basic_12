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
        Schema::dropIfExists("carrier_files");
        Schema::dropIfExists("carrier_terms");
        Schema::dropIfExists("carriers");
        Schema::dropIfExists("companies");
        Schema::dropIfExists("email_logs");
        Schema::dropIfExists("import_logs");
        Schema::dropIfExists("sql_logs");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
