<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Models\Team;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_monitors', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Site::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Team::class)->constrained()->onDelete('cascade');

            $table->dateTime('next_check')->nullable();

            $table->string('domain');
            $table->integer('port')->default(443);
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
        Schema::dropIfExists('certificate_monitors');
    }
};
