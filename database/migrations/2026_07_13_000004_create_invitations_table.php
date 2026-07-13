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
        Schema::create('invitations', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->foreignId('account_id')->comment('account the person is invited to')->constrained()->cascadeOnDelete();
            $table->string('email')->comment('email address the invitation was sent to');
            $table->string('role', 15)->default(PermissionEnum::Viewer->value)->comment('role granted once accepted');
            $table->string('token', 64)->unique()->comment('public token used to claim the invitation');
            $table->foreignId('invited_by')->nullable()->comment('user who sent the invitation')->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->comment('when the invitation stops being valid');
            $table->timestamp('accepted_at')->nullable()->comment('null until the invitation is claimed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
