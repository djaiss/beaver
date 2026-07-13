<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_user', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->foreignId('account_id')->comment('account the member belongs to')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->comment('user who is a member')->constrained()->cascadeOnDelete();
            $table->string('role', 15)->default(PermissionEnum::Viewer->value)->comment('member\'s role in the account');
            $table->foreignId('invited_by')->nullable()->comment('user who added this member')->constrained('users')->nullOnDelete();
            $table->timestamp('joined_at')->nullable()->comment('null until the invitation is accepted');
            $table->timestamps();

            $table->unique(['account_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_user');
    }
};
