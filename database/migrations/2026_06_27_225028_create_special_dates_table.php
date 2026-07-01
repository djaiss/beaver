<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('special_dates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('vault_id')->comment('vault the special date belongs to');
            $table->unsignedBigInteger('person_id')->comment('person associated with the special date');
            $table->boolean('should_be_reminded')->default(false)->comment('whether members should be reminded of the special date');
            $table->integer('year')->nullable()->comment('year of the special date');
            $table->integer('month')->nullable()->comment('month of the special date');
            $table->integer('day')->nullable()->comment('day of the special date');
            $table->text('name')->comment('encrypted name of the special date');
            $table->timestamps();

            $table->foreign('vault_id')->references('id')->on('vaults')->cascadeOnDelete();
            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_dates');
    }
};
