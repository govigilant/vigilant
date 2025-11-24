<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('healthcheck_results', function (Blueprint $table): void {
            $table->dropColumn('run_id');
            $table->timestamp('last_checked_at')->nullable()->after('data');
            $table->timestamp('last_unhealthy_at')->nullable()->after('last_checked_at');
            $table->unique(['healthcheck_id', 'key'], 'unique_healthcheck_result');
        });
    }

    public function down(): void
    {
        Schema::table('healthcheck_results', function (Blueprint $table): void {
            $table->dropUnique('unique_healthcheck_result');
            $table->dropColumn('last_unhealthy_at');
            $table->dropColumn('last_checked_at');
            $table->integer('run_id')->nullable()->after('healthcheck_id');
        });
    }
};
