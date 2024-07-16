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
        Schema::create('dns_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Site::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Team::class)->constrained()->onDelete('cascade');

            $table->string('type');
            $table->string('record');
            $table->string('value');
            $table->json('geoip')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dns_monitors');
    }
};
