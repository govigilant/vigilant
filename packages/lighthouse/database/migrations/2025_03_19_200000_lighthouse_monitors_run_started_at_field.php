<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lighthouse_monitors', function (Blueprint $table): void {
            $table->dateTime('run_started_at')->nullable()->after('next_run');
        });
    }

    public function down(): void
    {
        Schema::table('lighthouse_monitors', function (Blueprint $table): void {
            $table->dropColumn('run_started_at');
        });
    }
};
