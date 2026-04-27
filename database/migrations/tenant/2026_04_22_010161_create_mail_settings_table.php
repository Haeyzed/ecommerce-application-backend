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
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();

            // The mail driver (smtp, mailgun, postmark, etc.)
            $table->string('mailer')->default('smtp');

            // SMTP Host (e.g., smtp.mailtrap.io, smtp.gmail.com)
            $table->string('host')->nullable();

            // SMTP Port (e.g., 2525, 465, 587)
            $table->integer('port')->nullable();

            // SMTP Credentials
            $table->string('username')->nullable();
            $table->string('password')->nullable();

            // Encryption protocol (tls, ssl)
            $table->string('encryption')->nullable();

            // Global "From" details
            $table->string('from_address')->nullable();
            $table->string('from_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_settings');
    }
};
