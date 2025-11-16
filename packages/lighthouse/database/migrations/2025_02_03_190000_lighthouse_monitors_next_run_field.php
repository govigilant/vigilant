<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

return new class extends Migration
{
    public function up(): void
    {
        LighthouseMonitor::query()->withoutGlobalScopes()->update(['interval' => 60]);

        Schema::table('lighthouse_monitors', function (Blueprint $table): void {
            $table->integer('interval')->change();
            $table->dateTime('next_run')->nullable()->after('interval');

        });
    }

    public function down(): void
    {
        Schema::dropColumns('lighthouse_monitors', ['next_run']);

        Schema::table('lighthouse_monitors', function (Blueprint $table): void {
            $table->string('interval')->change();
        });
    }
};
