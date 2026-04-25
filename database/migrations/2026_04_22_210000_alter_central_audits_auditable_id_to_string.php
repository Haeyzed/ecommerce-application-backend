<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix audits.auditable_id for string primary keys (e.g. Stancl Tenant id).
     */
    public function up(): void
    {
        $connection = config('audit.drivers.database.connection', 'central');
        $table = config('audit.drivers.database.table', 'audits');

        if (! Schema::connection($connection)->hasTable($table)) {
            return;
        }

        $driver = Schema::connection($connection)->getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::connection($connection)->statement(
                "ALTER TABLE `{$table}` MODIFY `auditable_id` VARCHAR(255) NOT NULL"
            );

            return;
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = config('audit.drivers.database.connection', 'central');
        $table = config('audit.drivers.database.table', 'audits');

        if (! Schema::connection($connection)->hasTable($table)) {
            return;
        }

        if (Schema::connection($connection)->getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::connection($connection)->statement(
            "ALTER TABLE `{$table}` MODIFY `auditable_id` BIGINT UNSIGNED NOT NULL"
        );
    }
};
