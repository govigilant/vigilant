<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lighthouse_results', function (Blueprint $table) {
            $table->boolean('aggregated')->after('seo')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropColumns('lighthouse_results', ['aggregated']);
    }
};
