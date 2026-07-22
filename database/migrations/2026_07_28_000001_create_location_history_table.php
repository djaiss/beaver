<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_history', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('copy_id')->comment('copy that was stored');
            $table->unsignedBigInteger('location_id')->nullable()->comment('where the copy was stored, null when the location has since been deleted');
            $table->date('moved_at')->comment('when the copy arrived at the location');
            $table->date('moved_out_at')->nullable()->comment('when the copy left, null while it is still there (the open record)');
            $table->text('reason')->nullable()->comment('why the copy was moved, encrypted, null when unrecorded');
            $table->text('note')->nullable()->comment('free text about the move, encrypted, null when none');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the record');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the record');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('copy_id')->references('id')->on('copies')->cascadeOnDelete();
            // Deleting a location leaves the movement rows alone: they drop back to
            // an unknown location, the same way a copy's current location does.
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();

            // The records read in movement order, and the open record of a copy is
            // looked up by a null moved_out_at, so the indexes are shaped for both.
            $table->index(['copy_id', 'moved_at']);
            $table->index(['copy_id', 'moved_out_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_history');
    }
};
