<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dns_monitors', function (Blueprint $table): void {
            $table->string('value', 4096)->nullable()->change();
        });

        Schema::table('dns_monitor_histories', function (Blueprint $table): void {
            $table->string('value', 4096)->nullable()->change();
        });
    }
};
