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
        Schema::create('healthchecks', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Site::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Team::class)->constrained()->onDelete('cascade');
            $table->boolean('enabled')->default(true);

            $table->string('domain');
            $table->string('type');
            $table->string('endpoint')->nullable();

            $table->dateTime('next_check_at')->nullable();
            $table->dateTime('last_check_at')->nullable();
            $table->integer('interval');

            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('healthchecks');
    }
};
