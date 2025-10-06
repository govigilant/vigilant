<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uptime_monitors', function (Blueprint $table) {
            $table->float('latitude', 10, 6)->nullable()->after('timeout');
            $table->float('longitude', 10, 6)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('uptime_monitors', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
