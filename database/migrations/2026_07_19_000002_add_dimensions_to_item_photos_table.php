<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_photos', function (Blueprint $table): void {
            // Nullable because photos uploaded before this column existed have no
            // dimensions until the rebuild command reads them back off the disk.
            $table->unsignedInteger('width')->nullable()->after('size')->comment('width of the image in pixels');
            $table->unsignedInteger('height')->nullable()->after('width')->comment('height of the image in pixels');
        });
    }

    public function down(): void
    {
        Schema::table('item_photos', function (Blueprint $table): void {
            $table->dropColumn(['width', 'height']);
        });
    }
};
