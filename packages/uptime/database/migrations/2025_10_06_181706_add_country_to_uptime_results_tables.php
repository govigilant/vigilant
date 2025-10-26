<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uptime_results', function (Blueprint $table) {
            $table->string('country')->nullable()->after('total_time');
        });

        Schema::table('uptime_results_aggregates', function (Blueprint $table) {
            $table->string('country')->nullable()->after('total_time');
        });
    }

    public function down(): void
    {
        Schema::table('uptime_results', function (Blueprint $table) {
            $table->dropColumn('country');
        });

        Schema::table('uptime_results_aggregates', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
