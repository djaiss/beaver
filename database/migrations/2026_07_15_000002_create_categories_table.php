<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('catalog_id')->comment('collection the category belongs to');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('parent category, null when top level');
            $table->text('name')->comment('name of the category, e.g. Spider-Man');
            $table->text('description')->nullable()->comment('what the category holds, shown on the category page');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the category');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the category');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->unsignedBigInteger('deleted_by_id')->nullable()->comment('user who deleted the record');
            $table->text('deleted_by_name')->nullable()->comment('name of the user who deleted the record, at the time');
            $table->timestamps();
            $table->softDeletes()->comment('null unless the category has been soft deleted');

            $table->foreign('catalog_id')->references('id')->on('catalogs')->cascadeOnDelete();

            // Deleting a parent category leaves its children as top-level ones.
            $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
