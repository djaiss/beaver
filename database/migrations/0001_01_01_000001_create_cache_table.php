<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table): void {
            $table->string('key')->primary()->comment('cache key');
            $table->mediumText('value')->comment('cached value');
            $table->integer('expiration')->index()->comment('expiration timestamp');
        });

        Schema::create('cache_locks', function (Blueprint $table): void {
            $table->string('key')->primary()->comment('lock key');
            $table->string('owner')->comment('lock owner identifier');
            $table->integer('expiration')->index()->comment('lock expiration timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
