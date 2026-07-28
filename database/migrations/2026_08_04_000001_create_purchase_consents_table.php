<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // What somebody agreed to before being sent to a payment processor, one
        // row per point they confirmed. The row is evidence, so it outlives the
        // user who wrote it: deleting them clears the link and leaves the name.
        Schema::create('purchase_consents', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('account_id')->comment('the account being unlocked');
            $table->unsignedBigInteger('user_id')->nullable()->comment('the user who confirmed, null once they are deleted');
            $table->text('user_name')->comment('name of the user at the time, encrypted');
            $table->string('choice')->comment('what was confirmed, a PurchaseConsentChoice value');
            $table->text('ip_address')->nullable()->comment('the address the confirmation came from, encrypted, null when the request had none');
            $table->timestamp('accepted_at')->comment('when the point was confirmed');
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['account_id', 'accepted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_consents');
    }
};
