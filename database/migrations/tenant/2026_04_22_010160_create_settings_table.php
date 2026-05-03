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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->string('currency', 8)->default('USD');
            $table->string('timezone', 64)->default('UTC');
            $table->string('language', 8)->default('en');
//            $table->string('logo_path')->nullable();
//            $table->string('favicon_path')->nullable();
            $table->string('primary_color', 16)->nullable();
            $table->json('social')->nullable();
//            $table->json('payment_providers')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
