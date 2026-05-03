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
        Schema::connection('central')->table('settings', function (Blueprint $table) {
            $table->dropColumn('logo_path');
            $table->dropColumn('favicon_path');
            $table->dropColumn('payment_providers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->table('settings', function (Blueprint $table) {
            // Re-add columns if rolling back, consider default values if necessary
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->json('payment_providers')->nullable();
        });
    }
};
