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
        Schema::create('genders', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('vault_id')->comment('vault the gender belongs to');
            $table->text('name')->nullable()->comment('gender name');
            $table->text('name_translation_key')->nullable()->comment('gender name translation key');
            $table->integer('position')->comment('display order position');
            $table->timestamps();
            $table->foreign('vault_id')->references('id')->on('vaults')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genders');
    }
};
