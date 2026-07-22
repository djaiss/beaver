<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_logs', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('item_id')->comment('the item the action was performed on');
            $table->unsignedBigInteger('user_id')->nullable()->comment('user who performed the action');
            $table->text('user_name')->comment('name of the user at the time of the action');
            $table->text('action')->comment('action that was performed (also used as translation key)');
            $table->json('parameters')->nullable()->comment('parameters for the translation and the chip');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->index(['item_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_logs');
    }
};
