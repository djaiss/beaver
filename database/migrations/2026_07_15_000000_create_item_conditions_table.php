<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_conditions', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('account_id')->nullable()->comment('account the condition belongs to, null if a system default');
            $table->text('name')->comment('name of the condition, e.g. New or Used');
            $table->unsignedInteger('position')->default(0)->comment('orders conditions best to worst, so a return can be flagged as worse than it left');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the condition');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the condition');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_conditions');
    }
};
