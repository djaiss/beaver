<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valuations', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('copy_id')->comment('copy this values');
            $table->string('type')->default('user_estimate')->comment('where the valuation came from, a ValuationType value');
            $table->unsignedInteger('amount')->comment('what the copy was reckoned to be worth, in cents');
            $table->string('currency_code', 3)->nullable()->comment('ISO 4217 code the amount is in, null when unknown');
            $table->date('valued_at')->comment('date the copy was valued, which is what orders the valuations');
            $table->text('valuer')->nullable()->comment('who or what produced the valuation, null when unrecorded');
            $table->text('method')->nullable()->comment('how the figure was arrived at, null when unrecorded');
            $table->string('confidence')->default('unknown')->comment('how much weight the valuation carries, a ValuationConfidence value');
            $table->text('source_url')->nullable()->comment('where the valuation can be checked, null when there is none');
            $table->text('reference_number')->nullable()->comment('appraisal or report reference, null when there is none');
            $table->text('note')->nullable()->comment('free text about the valuation, null when none');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the valuation');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the valuation');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('copy_id')->references('id')->on('copies')->cascadeOnDelete();

            // The current estimated value of a copy is the latest of its
            // valuations, so that lookup is what the index is shaped for.
            $table->index(['copy_id', 'valued_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valuations');
    }
};
