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
            $table->unsignedBigInteger('catalog_id')->comment('collection the item belongs to');
            $table->unsignedBigInteger('category_id')->nullable()->comment('category the item sits in, null when uncategorized');
            $table->unsignedBigInteger('type_id')->nullable()->comment('type of the item, null when untyped, and always one of the types linked to the collection');
            $table->unsignedBigInteger('set_id')->nullable()->comment('set the item is part of, null when it belongs to no set');
            $table->unsignedBigInteger('series_id')->nullable()->comment('series the item belongs to, null when it belongs to no series');
            $table->text('name')->comment('name of the item, e.g. Amazing Spider-Man #1');
            $table->text('description')->nullable()->comment('free text description of the item');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the item');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the item');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->unsignedBigInteger('deleted_by_id')->nullable()->comment('user who deleted the record');
            $table->text('deleted_by_name')->nullable()->comment('name of the user who deleted the record, at the time');
            $table->timestamps();
            $table->softDeletes()->comment('null unless the item has been soft deleted');

            $table->foreign('catalog_id')->references('id')->on('catalogs')->cascadeOnDelete();

            // Deleting a type, a category, a set or a series leaves the items
            // alone: they drop back to none. Each of those is a grouping, and the
            // items exist on their own.
            $table->foreign('type_id')->references('id')->on('types')->nullOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->foreign('set_id')->references('id')->on('sets')->nullOnDelete();
            $table->foreign('series_id')->references('id')->on('series')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
