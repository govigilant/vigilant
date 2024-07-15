<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('lighthouse_sites', 'lighthouse_monitors');

        Schema::table('lighthouse_results', function (Blueprint $table): void {
            $table->renameColumn('lighthouse_site_id', 'lighthouse_monitor_id');
        });
    }

    public function down(): void
    {
        Schema::rename('lighthouse_monitors', 'lighthouse_sites');

        Schema::table('lighthouse_results', function (Blueprint $table): void {
            $table->renameColumn('lighthouse_monitor_id', 'lighthouse_site_id');
        });
    }
};
