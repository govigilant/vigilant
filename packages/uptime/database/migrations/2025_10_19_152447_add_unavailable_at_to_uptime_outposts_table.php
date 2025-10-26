<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uptime_outposts', function (Blueprint $table): void {
            $table->dateTime('unavailable_at')->nullable()->after('last_available_at');
        });
    }

    public function down(): void
    {
        Schema::table('uptime_outposts', function (Blueprint $table): void {
            $table->dropColumn('unavailable_at');
        });
    }
};
