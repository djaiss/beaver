<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->unsignedBigInteger('account_id')->comment('the account the document belongs to, for tenant scoping');
            $table->string('documentable_type')->comment('the kind of record the document is attached to, a morph map alias');
            $table->unsignedBigInteger('documentable_id')->comment('the id of the record the document is attached to');
            $table->string('type')->comment('what the document is, a DocumentType value');
            $table->text('name')->comment('the display name of the document, encrypted');
            $table->string('path')->nullable()->comment('the stored file path on the disk, app generated, null for an external document');
            $table->text('external_url')->nullable()->comment('a link to a file held elsewhere, encrypted, null for a stored document');
            $table->string('mime_type')->nullable()->comment('the mime type of the stored file, null for an external document');
            $table->unsignedInteger('size')->nullable()->comment('the size of the stored file in bytes, null for an external document');
            $table->text('description')->nullable()->comment('a free note about the document, encrypted, null when there is none');
            $table->date('issued_at')->nullable()->comment('when the document was issued, null when unknown');
            $table->text('reference_number')->nullable()->comment('an external reference such as an invoice or certificate number, encrypted, null when there is none');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who added the document');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the document');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();

            // The documents of a record are read together, and the account scope
            // filters every listing, so the indexes are shaped for both.
            $table->index(['documentable_type', 'documentable_id']);
            $table->index(['account_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
