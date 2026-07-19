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
            $table->unsignedBigInteger('series_id')->nullable()->after('set_id')->comment('series the item belongs to, null when it belongs to no series');

            // Deleting a series unlinks its items rather than taking them with it: a series is a
            // grouping, and the items exist on their own.
            $table->foreign('series_id')->references('id')->on('series')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->dropForeign(['series_id']);
            $table->dropColumn('series_id');
        });
    }
};
