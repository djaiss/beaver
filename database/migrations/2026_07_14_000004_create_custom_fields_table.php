<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_fields', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('type_id')->comment('type the field is attached to');
            $table->unsignedBigInteger('group_id')->nullable()->comment('group the field sits in, null when ungrouped');
            $table->text('name')->comment('name of the field, e.g. Issue # or Vintage');
            $table->string('field_type', 15)->comment('kind of field: text, number, date, boolean, select or rating');
            $table->json('options')->nullable()->comment('choices for select fields');
            $table->unsignedInteger('position')->default(0)->comment('order of the field within its group, or within the type when ungrouped');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the field');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the field');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('types')->cascadeOnDelete();

            // Deleting a group leaves its fields alone: they drop back to ungrouped.
            $table->foreign('group_id')->references('id')->on('custom_field_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
