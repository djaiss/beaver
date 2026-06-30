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
        Schema::create('webhook_endpoints', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('user_id')->comment('user who owns the endpoint');
            $table->text('label')->nullable()->comment('human readable name for the endpoint');
            $table->text('url')->comment('url the webhook is sent to');
            $table->text('secret')->comment('secret used to sign the webhook payload');
            $table->boolean('is_active')->default(true)->comment('whether the endpoint receives webhooks');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_endpoints');
    }
};
