<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uptime_monitors', function (Blueprint $table) {
            $table->string('country')->nullable()->after('timeout');
        });
    }

    public function down(): void
    {
        Schema::table('uptime_monitors', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
