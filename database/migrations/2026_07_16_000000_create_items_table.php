<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('collection_id')->comment('collection the item belongs to');
            $table->unsignedBigInteger('type_id')->nullable()->comment('type of the item, null when untyped, and always one of the types linked to the collection');
            $table->text('name')->comment('name of the item, e.g. Amazing Spider-Man #1');
            $table->text('description')->nullable()->comment('free text description of the item');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the item');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the item');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();
            $table->softDeletes()->comment('null unless the item has been soft deleted');

            $table->foreign('collection_id')->references('id')->on('collections')->cascadeOnDelete();

            // Deleting a type leaves its items alone: they drop back to untyped.
            $table->foreign('type_id')->references('id')->on('types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
