<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Users\Models\Team;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_monitor_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(CertificateMonitor::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Team::class)->constrained()->onDelete('cascade');

            $table->string('serial_number')->nullable();
            $table->string('protocol')->nullable();
            $table->string('fingerprint')->nullable();

            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();

            $table->json('data')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_monitor_histories');
    }
};
