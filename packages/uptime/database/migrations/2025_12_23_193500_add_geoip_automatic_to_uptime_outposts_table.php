<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uptime_outposts', function (Blueprint $table): void {
            $table->boolean('geoip_automatic')->default(true)->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('uptime_outposts', function (Blueprint $table): void {
            $table->dropColumn('geoip_automatic');
        });
    }
};
