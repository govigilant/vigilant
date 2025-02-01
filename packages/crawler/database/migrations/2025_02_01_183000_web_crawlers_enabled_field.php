<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_crawlers', function (Blueprint $table) {
            $table->boolean('enabled')->default(true)->after('id');
        });
    }

    public function down(): void
    {
        Schema::dropColumns('web_crawlers', ['enabled']);
    }
};
