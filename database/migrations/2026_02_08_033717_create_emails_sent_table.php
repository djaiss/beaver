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
        Schema::create('emails_sent', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('user_id')->nullable()->comment('user who received the email');
            $table->string('uuid')->nullable()->comment('unique identifier for the email');
            $table->text('email_type')->comment('type of email sent');
            $table->text('email_address')->comment('recipient email address');
            $table->text('subject')->nullable()->comment('email subject line');
            $table->text('body')->nullable()->comment('email body content');
            $table->datetime('sent_at')->nullable()->comment('timestamp when the email was sent');
            $table->datetime('delivered_at')->nullable()->comment('timestamp when the email was delivered');
            $table->datetime('bounced_at')->nullable()->comment('timestamp when the email bounced');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails_sent');
    }
};
