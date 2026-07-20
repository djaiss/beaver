<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_records', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('copy_id')->comment('copy this covers');
            $table->text('provider')->comment('the insurer, encrypted');
            $table->text('policy_number')->nullable()->comment('the policy number, encrypted, null when unrecorded');
            $table->text('coverage_type')->nullable()->comment('what kind of coverage this is, encrypted, null when unrecorded');
            $table->unsignedInteger('insured_value')->comment('what the copy is insured for, in cents');
            $table->string('currency_code', 3)->nullable()->comment('ISO 4217 code the insured value is in, null when unknown');
            $table->unsignedInteger('deductible_amount')->nullable()->comment('the deductible, in cents, null when none');
            $table->string('deductible_currency_code', 3)->nullable()->comment('ISO 4217 code the deductible is in, null when none');
            $table->date('starts_at')->nullable()->comment('when the coverage begins, null when unrecorded');
            $table->date('ends_at')->nullable()->comment('when the coverage ends, null when ongoing');
            $table->string('status')->default('active')->comment('where the coverage stands, an InsuranceStatus value');
            $table->boolean('is_scheduled_item')->default(false)->comment('whether the copy is individually listed on the policy');
            $table->text('contact_name')->nullable()->comment('broker or agent name, encrypted, null when none');
            $table->text('contact_email')->nullable()->comment('broker or agent email, encrypted, null when none');
            $table->text('contact_phone')->nullable()->comment('broker or agent phone, encrypted, null when none');
            $table->text('note')->nullable()->comment('free text about the coverage, encrypted, null when none');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the record');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the record');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('copy_id')->references('id')->on('copies')->cascadeOnDelete();

            // The active record of a copy is looked up by status, and the records
            // read in coverage order, so the index is shaped for both.
            $table->index(['copy_id', 'status']);
            $table->index(['copy_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_records');
    }
};
