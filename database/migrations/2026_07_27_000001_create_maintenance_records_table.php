<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('copy_id')->comment('copy the work was performed on');
            $table->unsignedBigInteger('provenance_event_id')->nullable()->comment('the provenance event this record generated, null when it is not part of provenance');
            $table->string('type')->comment('what kind of work this was, a MaintenanceType value');
            $table->text('title')->comment('short human readable summary, encrypted');
            $table->text('description')->nullable()->comment('the detail behind the title, encrypted, null when there is none');
            $table->text('performed_by')->nullable()->comment('who did the work, encrypted, null when unrecorded');
            $table->date('performed_at')->nullable()->comment('when the work was done, null when unrecorded');
            $table->unsignedInteger('cost_amount')->nullable()->comment('what the work cost, in cents, null when free or unrecorded');
            $table->string('cost_currency_code', 3)->nullable()->comment('ISO 4217 code the cost is in, null when no cost');
            $table->unsignedBigInteger('item_condition_before_id')->nullable()->comment('the copy condition before the work, null when unrecorded');
            $table->unsignedBigInteger('item_condition_after_id')->nullable()->comment('the copy condition after the work, null when unrecorded');
            $table->date('next_due_at')->nullable()->comment('when this care is next due, null when it is not recurring');
            $table->boolean('include_in_provenance')->default(false)->comment('whether this record is significant enough to belong to the object story');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the record');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the record');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('copy_id')->references('id')->on('copies')->cascadeOnDelete();
            $table->foreign('provenance_event_id')->references('id')->on('provenance_events')->nullOnDelete();
            $table->foreign('item_condition_before_id')->references('id')->on('item_conditions')->nullOnDelete();
            $table->foreign('item_condition_after_id')->references('id')->on('item_conditions')->nullOnDelete();

            // The records read newest first per copy, and the due ones are looked
            // up by their next due date, so the indexes are shaped for both.
            $table->index(['copy_id', 'performed_at']);
            $table->index(['copy_id', 'next_due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
