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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            // sick | vacation | maternity | unpaid | other
            $table->string('type', 32);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days', 8, 2);
            $table->text('reason')->nullable();

            // pending | approved | rejected | cancelled
            $table->string('status', 32)->default('pending');
            $table->unsignedBigInteger('approved_by_employee_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
