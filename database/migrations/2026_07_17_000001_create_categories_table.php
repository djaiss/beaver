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
            $table->unsignedBigInteger('collection_id')->comment('collection the category belongs to');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('parent category, null when top level');
            $table->text('name')->comment('name of the category, e.g. Spider-Man');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the category');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the category');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();
            $table->softDeletes()->comment('null unless the category has been soft deleted');

            $table->foreign('collection_id')->references('id')->on('collections')->cascadeOnDelete();

            // Deleting a parent category leaves its children as top-level ones.
            $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
