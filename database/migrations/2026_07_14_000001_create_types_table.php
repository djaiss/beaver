<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('types', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->foreignId('account_id')->comment('account the type belongs to')->constrained()->cascadeOnDelete();
            $table->text('name')->comment('name of the type, e.g. Comics or Vinyl');
            $table->string('color', 7)->comment('hex color representing the type, e.g. #1D4ED8');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the type');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the type');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('types');
    }
};
