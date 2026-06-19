<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
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
        Schema::create('members', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('vault_id')->comment('vault the member belongs to');
            $table->unsignedBigInteger('user_id')->nullable()->comment('user account of the member');
            $table->unsignedBigInteger('last_person_seen_id')->nullable();
            $table->timestamp('joined_at')->comment('timestamp when the member joined the vault');
            $table->string('timezone', 50)->nullable()->comment('member\'s preferred timezone');
            $table->string('role', 15)->default(PermissionEnum::Viewer->value)->comment('member\'s role in the vault');
            $table->timestamps();

            $table->foreign('vault_id')->references('id')->on('vaults')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('last_person_seen_id')->references('id')->on('persons')->onDelete('set null');

            $table->unique(['vault_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
