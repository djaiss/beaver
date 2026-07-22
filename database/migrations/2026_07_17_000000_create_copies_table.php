<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('copies', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('item_id')->comment('item this is a physical copy of');
            $table->text('identifier')->nullable()->comment('account defined identifier for this copy, null when it has none');
            $table->unsignedBigInteger('item_condition_id')->nullable()->comment('current condition of the copy, null when unknown');
            $table->unsignedBigInteger('current_location_id')->nullable()->comment('where the copy is currently stored, mirrors the open location history record, null when unknown');
            $table->string('status')->default('owned')->comment('where the copy sits in its lifecycle, a CopyStatus value');
            $table->unsignedInteger('quantity')->default(1)->comment('how many identical instances this row stands for');
            $table->date('disposed_at')->nullable()->comment('date the copy left the collection, null while it is still held');
            $table->text('note')->nullable()->comment('free text about the copy, null when none');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the copy');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the copy');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();
            $table->softDeletes()->comment('null unless the copy has been soft deleted');

            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();

            // Deleting a condition or location leaves its copies alone: they drop back to none.
            $table->foreign('item_condition_id')->references('id')->on('item_conditions')->nullOnDelete();
            $table->foreign('current_location_id')->references('id')->on('locations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('copies');
    }
};
