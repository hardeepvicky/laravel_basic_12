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
        Schema::rename('exp_party_address', 'exp_party_addresses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('exp_party_addresses', 'exp_party_address');
    }
};
