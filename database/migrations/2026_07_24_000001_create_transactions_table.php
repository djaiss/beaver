<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('copy_id')->comment('copy the exchange involved');
            $table->string('type')->comment('what kind of exchange this was, a TransactionType value');
            $table->text('counterparty')->nullable()->comment('who the other side was, null when unrecorded');
            $table->unsignedInteger('amount')->nullable()->comment('the sum itself, in cents, before tax, fees and shipping');
            $table->string('currency_code', 3)->nullable()->comment('ISO 4217 code every amount on the row is in');
            $table->unsignedInteger('tax_amount')->nullable()->comment('tax paid, in cents');
            $table->unsignedInteger('fee_amount')->nullable()->comment('fees paid, in cents');
            $table->unsignedInteger('shipping_amount')->nullable()->comment('shipping paid, in cents');
            $table->unsignedInteger('total_amount')->nullable()->comment('what changed hands in total, in cents');
            $table->date('occurred_at')->comment('date the exchange happened, which is what orders the transactions');
            $table->text('reference_number')->nullable()->comment('invoice, order, lot or transaction reference, null when there is none');
            $table->text('source_url')->nullable()->comment('where the transaction can be checked, null when there is none');
            $table->text('note')->nullable()->comment('free text about the transaction, null when none');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the transaction');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the transaction');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('copy_id')->references('id')->on('copies')->cascadeOnDelete();

            // The acquisition of a copy is the earliest qualifying transaction,
            // and the panel lists them newest first, so both read this way.
            $table->index(['copy_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
