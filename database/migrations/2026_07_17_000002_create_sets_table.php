<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sets', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('account_id')->comment('account the set belongs to');
            $table->text('name')->comment('name of the set, e.g. Amazing Spider-Man #1-10');
            $table->text('description')->nullable()->comment('free text description of the set');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the set');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the set');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();
            $table->softDeletes()->comment('null unless the set has been soft deleted');

            $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sets');
    }
};
