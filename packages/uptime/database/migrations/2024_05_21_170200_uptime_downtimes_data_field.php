<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uptime_downtimes', function (Blueprint $table) {
            $table->json('data')->nullable()->after('end');
        });
    }

    public function down(): void
    {
        Schema::dropColumns('uptime_downtimes', ['data']);
    }
};
