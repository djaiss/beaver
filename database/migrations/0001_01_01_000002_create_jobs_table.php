<?php

declare(strict_types=1);

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
        Schema::create('jobs', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->string('queue')->index()->comment('queue name');
            $table->longText('payload')->comment('serialized job payload');
            $table->unsignedTinyInteger('attempts')->comment('number of attempts');
            $table->unsignedInteger('reserved_at')->nullable()->comment('timestamp when job was reserved');
            $table->unsignedInteger('available_at')->comment('timestamp when job becomes available');
            $table->unsignedInteger('created_at')->comment('job creation timestamp');
        });

        Schema::create('job_batches', function (Blueprint $table): void {
            $table->string('id')->primary()->comment('batch identifier');
            $table->string('name')->comment('batch name');
            $table->integer('total_jobs')->comment('total number of jobs in the batch');
            $table->integer('pending_jobs')->comment('number of pending jobs');
            $table->integer('failed_jobs')->comment('number of failed jobs');
            $table->longText('failed_job_ids')->comment('IDs of failed jobs');
            $table->mediumText('options')->nullable()->comment('batch options');
            $table->integer('cancelled_at')->nullable()->comment('cancellation timestamp');
            $table->integer('created_at')->comment('batch creation timestamp');
            $table->integer('finished_at')->nullable()->comment('batch completion timestamp');
        });

        Schema::create('failed_jobs', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->string('uuid')->unique()->comment('unique identifier');
            $table->text('connection')->comment('queue connection name');
            $table->text('queue')->comment('queue name');
            $table->longText('payload')->comment('serialized job payload');
            $table->longText('exception')->comment('exception thrown');
            $table->timestamp('failed_at')->useCurrent()->comment('failure timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
