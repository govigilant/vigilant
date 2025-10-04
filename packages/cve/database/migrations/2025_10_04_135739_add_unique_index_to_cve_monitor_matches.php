<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cve_monitor_matches', function (Blueprint $table): void {
            $table->unique(['cve_id', 'cve_monitor_id'], 'unique_cve_monitor_match');
        });
    }

    public function down(): void
    {
        Schema::table('cve_monitor_matches', function (Blueprint $table): void {
            $table->dropUnique('unique_cve_monitor_match');
        });
    }
};
