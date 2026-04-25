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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event')->index(); // order.placed, ticket.replied, etc.
            $table->string('channel'); // matches notification_channels.key
            $table->string('subject')->nullable();
            $table->longText('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['event','channel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
