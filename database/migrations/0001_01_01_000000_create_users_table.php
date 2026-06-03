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
        Schema::create('users', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->text('first_name')->comment('user\'s first name');
            $table->text('last_name')->comment('user\'s last name');
            $table->text('nickname')->nullable()->comment('user\'s nickname');
            $table->string('email')->unique()->comment('user\'s email address');
            $table->timestamp('email_verified_at')->nullable()->comment('email verification timestamp');
            $table->string('password')->comment('user\'s password');
            $table->text('two_factor_secret')->nullable()->comment('user\'s two factor authentication secret');
            $table->text('two_factor_recovery_codes')->nullable()->comment('user\'s two factor recovery codes');
            $table->timestamp('two_factor_confirmed_at')->nullable()->comment('two factor confirmation timestamp');
            $table->datetime('trial_ends_at')->nullable()->comment('trial end timestamp');
            $table->text('last_used_ip')->nullable()->comment('last used IP address');
            $table->datetime('last_activity_at')->nullable()->comment('last activity timestamp');
            $table->string('locale', 5)->default('en')->comment('user\'s locale');
            $table->boolean('time_format_24h')->default(true)->comment('time format preference');
            $table->boolean('auto_delete_account')->default(false)->comment('auto delete account preference');
            $table->rememberToken()->comment('remember token');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary()->comment('user\'s email address');
            $table->string('token')->comment('password reset token');
            $table->timestamp('created_at')->nullable()->comment('token creation timestamp');
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary()->comment('session ID');
            $table->foreignId('user_id')->nullable()->index()->comment('user ID');
            $table->string('ip_address', 45)->nullable()->comment('IP address');
            $table->text('user_agent')->nullable()->comment('user agent');
            $table->longText('payload')->comment('session payload');
            $table->integer('last_activity')->index()->comment('last activity timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
