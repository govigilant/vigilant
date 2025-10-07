<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uptime_monitors', function (Blueprint $table): void {
            $table->timestamp('geoip_fetched_at')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('uptime_monitors', function (Blueprint $table): void {
            $table->dropColumn('geoip_fetched_at');
        });
    }
};
