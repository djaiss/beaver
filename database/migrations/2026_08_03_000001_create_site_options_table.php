<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A single row holding the instance wide options an administrator edits
        // from the panel. It is created the first time the form is saved.
        Schema::create('site_options', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->boolean('banner_enabled')->default(false)->comment('whether the announcement banner shows on the marketing site');
            $table->text('banner_version')->nullable()->comment('optional version pill, such as v0.9, encrypted');
            $table->text('banner_url')->nullable()->comment('optional URL the banner links to, encrypted');
            $table->text('banner_content')->nullable()->comment('per locale sentence and link label, keyed by locale, encrypted json');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_options');
    }
};
