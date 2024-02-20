<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignIdFor(\Vigilant\Users\Models\Team::class);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
