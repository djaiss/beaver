<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_field_values', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('item_id')->comment('item the value belongs to');
            $table->unsignedBigInteger('custom_field_id')->comment('field the value answers');
            $table->text('value')->nullable()->comment('the value the user entered for the field');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('custom_field_id')->references('id')->on('custom_fields')->cascadeOnDelete();

            $table->unique(['item_id', 'custom_field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_field_values');
    }
};
