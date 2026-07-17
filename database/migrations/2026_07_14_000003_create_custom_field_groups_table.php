<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_field_groups', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('type_id')->comment('type the group belongs to');
            $table->text('name')->comment('name of the group, e.g. Main or Details');
            $table->unsignedInteger('position')->default(0)->comment('order of the group within the type');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the group');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the group');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('types')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_field_groups');
    }
};
