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
        Schema::create(config('magiclink.magiclink_table', 'magic_links'), function (Blueprint $table): void {
            $table->uuid('id')->primary()->comment('primary key');
            $table->string('token', 255)->comment('authentication token');
            $table->text('action')->comment('serialized action to execute when the link is visited');
            $table->unsignedTinyInteger('num_visits')->default(0)->comment('number of times the link has been visited');
            $table->unsignedTinyInteger('max_visits')->nullable()->comment('maximum number of allowed visits');
            $table->timestamp('available_at')->nullable()->comment('timestamp when the link becomes available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('magiclink.magiclink_table', 'magic_links'));
    }
};
