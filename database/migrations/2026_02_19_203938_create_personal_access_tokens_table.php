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
        Schema::create('personal_access_tokens', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->morphs('tokenable');
            $table->text('name')->comment('token name');
            $table->string('token', 64)->unique()->comment('hashed token value');
            $table->text('abilities')->nullable()->comment('token abilities');
            $table->timestamp('last_used_at')->nullable()->comment('timestamp when the token was last used');
            $table->timestamp('expires_at')->nullable()->index()->comment('token expiration timestamp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
