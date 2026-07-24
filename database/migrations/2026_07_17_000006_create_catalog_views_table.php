<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_views', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('user_id')->comment('user whose view preference this is');
            $table->unsignedBigInteger('catalog_id')->comment('collection the preference applies to');
            $table->string('items_view')->default('grid')->comment('the last items view the user opened: grid, list or table');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('catalog_id')->references('id')->on('catalogs')->cascadeOnDelete();

            // One remembered view per user per collection.
            $table->unique(['user_id', 'catalog_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_views');
    }
};
