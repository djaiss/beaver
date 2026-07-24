<?php

declare(strict_types=1);

use App\Enums\VisibilityEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogs', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->uuid('uuid')->unique()->comment('public-facing identifier');
            $table->foreignId('account_id')->comment('account the collection belongs to')->constrained()->cascadeOnDelete();
            $table->text('name')->comment('name of the collection');
            $table->text('description')->nullable()->comment('free text description of the collection');
            $table->string('emoji')->nullable()->comment('emoji representing the collection');
            $table->string('visibility', 15)->default(VisibilityEnum::Private->value)->comment('who can see the catalog: private, shared or public');
            $table->string('currency', 3)->nullable()->comment('currency used for valuation totals');
            $table->json('settings')->nullable()->comment('per-collection preferences');
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('user who created the collection');
            $table->text('created_by_name')->nullable()->comment('name of the creator at the time');
            $table->unsignedBigInteger('updated_by_id')->nullable()->comment('user who last updated the collection');
            $table->text('updated_by_name')->nullable()->comment('name of the last editor at the time');
            $table->timestamps();
            $table->softDeletes()->comment('null unless the collection has been soft deleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogs');
    }
};
