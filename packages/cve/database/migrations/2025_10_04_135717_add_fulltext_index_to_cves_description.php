<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only create FULLTEXT index for MySQL/MariaDB
        // SQLite doesn't support FULLTEXT indexes
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement('CREATE FULLTEXT INDEX idx_cves_description_fulltext ON cves(description)');
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement('DROP INDEX idx_cves_description_fulltext ON cves');
        }
    }
};
