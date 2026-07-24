<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->unsignedBigInteger('category_id')->nullable()->after('catalog_id')->comment('category the item sits in, null when uncategorized');
            $table->unsignedBigInteger('set_id')->nullable()->after('type_id')->comment('set the item is part of, null when it belongs to no set');

            // Deleting a category or set leaves its items alone: they drop back to none.
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->foreign('set_id')->references('id')->on('sets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['set_id']);
            $table->dropColumn(['category_id', 'set_id']);
        });
    }
};
