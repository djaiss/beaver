<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The tables holding objects the trash screen lists.
     *
     * @var list<string>
     */
    private array $tables = ['catalogs', 'items', 'copies', 'categories', 'sets'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->unsignedBigInteger('deleted_by_id')->nullable()->after('updated_by_name')->comment('user who deleted the record');
                $table->text('deleted_by_name')->nullable()->after('deleted_by_id')->comment('name of the user who deleted the record, at the time');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->dropColumn(['deleted_by_id', 'deleted_by_name']);
            });
        }
    }
};
