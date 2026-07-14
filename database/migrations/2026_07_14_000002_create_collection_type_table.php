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
            $table->foreignId('collection_id')->comment('the linked collection')->constrained()->cascadeOnDelete();
            $table->foreignId('type_id')->comment('the linked type')->constrained()->cascadeOnDelete();
            $table->unique(['collection_id', 'type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_type');
    }
};
