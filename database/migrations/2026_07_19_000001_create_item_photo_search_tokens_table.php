<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_photo_search_tokens', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('item_photo_id')->comment('photo the token makes searchable');
            $table->char('token', 64)->comment('keyed hash of one word, or of one prefix of one word, taken from the file name and the name of the item');
            $table->timestamps();

            $table->unique(['item_photo_id', 'token']);
            $table->index('token');

            $table->foreign('item_photo_id')->references('id')->on('item_photos')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_photo_search_tokens');
    }
};
