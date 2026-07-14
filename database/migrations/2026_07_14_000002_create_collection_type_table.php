<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_type', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('collection_id')->comment('the linked collection');
            $table->unsignedBigInteger('type_id')->comment('the linked type');
            $table->unique(['collection_id', 'type_id']);

            $table->foreign('collection_id')->references('id')->on('collections')->cascadeOnDelete();
            $table->foreign('type_id')->references('id')->on('types')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_type');
    }
};
