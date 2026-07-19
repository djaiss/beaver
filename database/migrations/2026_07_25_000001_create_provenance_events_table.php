<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provenance_events', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('copy_id')->comment('copy whose story this is part of');
            $table->unsignedBigInteger('transaction_id')->nullable()->comment('transaction this event came from, null when it had none');
            $table->string('type')->comment('what kind of moment this was, a ProvenanceEventType value');
            $table->text('title')->comment('short human readable summary, shown in the timeline');
            $table->text('description')->nullable()->comment('the detail behind the title, null when there is none');
            $table->date('occurred_at')->nullable()->comment('when it happened, null when the date is unknown');
            $table->string('occurred_at_precision')->default('exact')->comment('how much of the date is known, a DatePrecision value');
            $table->text('location')->nullable()->comment('where it happened, null when unrecorded');
            $table->text('from_party')->nullable()->comment('who the object came from, null when unrecorded');
            $table->text('to_party')->nullable()->comment('who the object went to, null when unrecorded');
            $table->text('reference_number')->nullable()->comment('auction lot, certificate number or archive reference');
            $table->text('source_url')->nullable()->comment('where the event can be checked, null when there is none');
            $table->boolean('is_verified')->default(false)->comment('whether evidence backs this event');
            $table->text('verification_note')->nullable()->comment('how it was verified, null when unrecorded');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the event');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the event');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('copy_id')->references('id')->on('copies')->cascadeOnDelete();

            // Deleting a transaction unlinks the event rather than deleting it.
            // The money was a fact about the exchange; the moment in the object's
            // story outlives the record of what was paid for it.
            $table->foreign('transaction_id')->references('id')->on('transactions')->nullOnDelete();

            // A transaction is one exchange, so it cannot be the source of two
            // separate moments in the story. Null is exempt, so any number of
            // events may carry no transaction at all.
            $table->unique('transaction_id');

            // The panel reads a copy's events oldest first, since provenance
            // reads as a narrative rather than as a feed.
            $table->index(['copy_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provenance_events');
    }
};
