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
        Schema::create('cve_monitors', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Site::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Team::class)->constrained()->onDelete('cascade');

            $table->boolean('enabled')->default(true);
            $table->string('keyword');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cve_monitors');
    }
};
