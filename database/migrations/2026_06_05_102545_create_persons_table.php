<?php

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
        Schema::create('persons', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('vault_id')->comment('vault the person belongs to');
            $table->unsignedBigInteger('gender_id')->nullable()->comment('gender associated with the person');
            $table->text('marital_status')->nullable()->comment('person marital status');
            $table->text('kids_status')->nullable()->comment('person kids status');
            $table->text('slug')->nullable()->comment('person URL slug');
            $table->text('first_name')->nullable()->comment('person first name');
            $table->text('middle_name')->nullable()->comment('person middle name');
            $table->text('last_name')->nullable()->comment('person last name');
            $table->text('nickname')->nullable()->comment('person nickname');
            $table->text('maiden_name')->nullable()->comment('person maiden name');
            $table->text('suffix')->nullable()->comment('person name suffix');
            $table->text('prefix')->nullable()->comment('person name prefix');
            $table->boolean('can_be_deleted')->default(true)->comment('whether the person can be deleted');
            $table->boolean('is_listed')->default(true)->comment('whether the person is listed');
            $table->timestamps();
            $table->foreign('vault_id')->references('id')->on('vaults')->onDelete('cascade');
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
