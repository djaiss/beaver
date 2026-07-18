<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_tag', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('item_id')->comment('the tagged item');
            $table->unsignedBigInteger('tag_id')->comment('the tag applied to the item');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();

            $table->unique(['item_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_tag');
    }
};
