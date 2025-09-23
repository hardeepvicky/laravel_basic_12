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
        Schema::create('exp_lead_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId("exp_lead_id")->constrained("exp_leads")->onDelete("restrict");
            $table->foreignId("document_file_id")->constrained("document_files")->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exp_lead_files');
    }
};
