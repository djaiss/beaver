<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('account_id')->comment('account the location belongs to');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('parent location, for nesting');
            $table->text('name')->comment('name of the location, e.g. Shelf A or Box 3');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the location');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the location');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('locations')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
