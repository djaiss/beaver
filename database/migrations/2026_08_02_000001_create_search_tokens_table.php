<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_tokens', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('account_id')->comment('account the searchable record belongs to');
            $table->string('searchable_type', 50)->comment('the kind of record the token makes searchable, as a stable alias rather than a class name');
            $table->unsignedBigInteger('searchable_id')->comment('record the token makes searchable');
            $table->char('token', 64)->comment('keyed hash of one word, or of one prefix of one word, taken from the text of the record');
            $table->unsignedSmallInteger('weight')->comment('how strong a match on this token is: 100 for a name, 30 for a description');
            $table->timestamps();

            $table->unique(['searchable_type', 'searchable_id', 'token']);
            $table->index(['account_id', 'token']);
            $table->index(['searchable_type', 'searchable_id']);

            $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_tokens');
    }
};
