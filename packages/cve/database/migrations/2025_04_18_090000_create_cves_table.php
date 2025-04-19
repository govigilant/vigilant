<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cves', function (Blueprint $table): void {
            $table->id();

            $table->string('identifier');
            $table->float('score')->nullable();
            $table->text('description');

            $table->dateTime('published_at');
            $table->dateTime('modified_at');

            $table->json('data');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cves');
    }
};
