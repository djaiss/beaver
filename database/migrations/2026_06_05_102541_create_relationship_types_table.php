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
        Schema::create('relationship_types', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('vault_id')->comment('vault the relationship type belongs to');
            $table->unsignedBigInteger('relationship_type_category_id')->comment('category the relationship type belongs to');
            $table->string('key')->comment('stable relationship type key');
            $table->text('name')->nullable()->comment('relationship type name');
            $table->text('name_translation_key')->nullable()->comment('relationship type name translation key');
            $table->text('forward_name_translation_key')->nullable()->comment('forward relationship name translation key');
            $table->text('reverse_name_translation_key')->nullable()->comment('reverse relationship name translation key');
            $table->boolean('is_directed')->default(false)->comment('whether the relationship has different meanings depending on direction');
            $table->boolean('can_be_deleted')->default(true)->comment('whether the relationship type can be deleted');
            $table->integer('position')->comment('display order position');
            $table->timestamps();
            $table->foreign('vault_id')->references('id')->on('vaults')->onDelete('cascade');
            $table->foreign('relationship_type_category_id')->references('id')->on('relationship_type_categories')->onDelete('cascade');
            $table->unique(['relationship_type_category_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relationship_types');
    }
};
