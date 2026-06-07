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
        Schema::create('relationship_type_categories', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('vault_id')->comment('vault the relationship type category belongs to');
            $table->string('key')->comment('stable relationship type category key');
            $table->text('name')->nullable()->comment('relationship type category name');
            $table->text('name_translation_key')->nullable()->comment('relationship type category name translation key');
            $table->integer('position')->comment('display order position');
            $table->boolean('can_be_deleted')->default(true)->comment('whether the relationship type category can be deleted');
            $table->timestamps();
            $table->foreign('vault_id')->references('id')->on('vaults')->onDelete('cascade');
            $table->unique(['vault_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relationship_type_categories');
    }
};
