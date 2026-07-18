<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('copies', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('item_id')->comment('item this is a physical copy of');
            $table->unsignedBigInteger('condition_id')->nullable()->comment('condition of the copy, null when unknown');
            $table->unsignedBigInteger('location_id')->nullable()->comment('where the copy is physically stored, null when unknown');
            $table->date('acquired_at')->nullable()->comment('date the copy was obtained, null when unknown');
            $table->unsignedInteger('price_paid')->nullable()->comment('amount paid for the copy, in cents, null when unknown');
            $table->unsignedInteger('estimated_value')->nullable()->comment('estimated worth of the copy, in cents, null when unknown');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the copy');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the copy');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();
            $table->softDeletes()->comment('null unless the copy has been soft deleted');

            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();

            // Deleting a condition or location leaves its copies alone: they drop back to none.
            $table->foreign('condition_id')->references('id')->on('conditions')->nullOnDelete();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('copies');
    }
};
