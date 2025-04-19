<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cve_monitor_matches', function (Blueprint $table): void {
            $table->id();

            $table->foreignIdFor(Cve::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(CveMonitor::class)->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cve_monitor_matches');
    }
};
