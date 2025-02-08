<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lighthouse_results', function (Blueprint $table): void {
            $table->uuid('batch_id')->after('team_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('lighthouse_results', function (Blueprint $table): void {
            $table->dropColumn('batch_id');
        });
    }
};
