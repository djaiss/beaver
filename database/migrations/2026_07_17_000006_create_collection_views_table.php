<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_views', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('user_id')->comment('user whose view preference this is');
            $table->unsignedBigInteger('collection_id')->comment('collection the preference applies to');
            $table->string('items_view')->default('grid')->comment('the last items view the user opened: grid, list or table');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('collection_id')->references('id')->on('collections')->cascadeOnDelete();

            // One remembered view per user per collection.
            $table->unique(['user_id', 'collection_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_views');
    }
};
