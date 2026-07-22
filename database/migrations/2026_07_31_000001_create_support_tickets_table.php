<?php

declare(strict_types=1);

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
        Schema::create('support_tickets', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('user_id')->comment('user who opened the conversation');
            $table->text('subject')->comment('short summary, derived from the first message');
            $table->string('category')->comment('what the conversation is about');
            $table->string('status')->default('open')->comment('open or closed');
            $table->string('closed_by')->nullable()->comment('who closed the conversation: user or team');
            $table->timestamp('closed_at')->nullable()->comment('when the conversation was closed');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
