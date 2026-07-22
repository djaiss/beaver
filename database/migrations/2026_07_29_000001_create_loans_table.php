<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('copy_id')->comment('copy whose custody moved');
            $table->unsignedBigInteger('loan_provenance_event_id')->nullable()->comment('the provenance event the loan generated, null when it is not part of provenance');
            $table->unsignedBigInteger('return_provenance_event_id')->nullable()->comment('the provenance event the return generated, null until returned or when not part of provenance');
            $table->string('direction')->comment('which way custody moved, a LoanDirection value');
            $table->string('status')->comment('where the loan sits in its lifecycle, a LoanStatus value');
            $table->text('party')->comment('who the copy was lent to or borrowed from, encrypted');
            $table->text('purpose')->nullable()->comment('why the copy was loaned, encrypted, null when there is none');
            $table->date('loaned_at')->comment('when the copy left or arrived');
            $table->date('due_at')->nullable()->comment('when the copy is expected back or due to be returned, null when open ended');
            $table->date('returned_at')->nullable()->comment('when the loan was closed, null while it is still out');
            $table->unsignedBigInteger('item_condition_out_id')->nullable()->comment('the copy condition when it left, null when unrecorded');
            $table->unsignedBigInteger('item_condition_in_id')->nullable()->comment('the copy condition when it came back, null until returned or unrecorded');
            $table->unsignedInteger('deposit_amount')->nullable()->comment('a deposit held against the loan, in cents, null when there is none');
            $table->string('deposit_currency_code', 3)->nullable()->comment('ISO 4217 code the deposit is in, null when no deposit');
            $table->boolean('include_in_provenance')->default(false)->comment('whether this loan belongs to the object story');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the loan');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the loan');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('copy_id')->references('id')->on('copies')->cascadeOnDelete();
            $table->foreign('loan_provenance_event_id')->references('id')->on('provenance_events')->nullOnDelete();
            $table->foreign('return_provenance_event_id')->references('id')->on('provenance_events')->nullOnDelete();
            $table->foreign('item_condition_out_id')->references('id')->on('item_conditions')->nullOnDelete();
            $table->foreign('item_condition_in_id')->references('id')->on('item_conditions')->nullOnDelete();

            // The loans read newest first per copy, and the scheduled job that
            // flips them to overdue looks them up by status and due date, so the
            // indexes are shaped for both.
            $table->index(['copy_id', 'loaned_at']);
            $table->index(['status', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
