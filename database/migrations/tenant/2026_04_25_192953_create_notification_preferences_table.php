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
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->morphs('notifiable'); // customer or admin
            $table->string('event');
            $table->string('channel');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->unique(['notifiable_type','notifiable_id','event','channel'], 'np_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
