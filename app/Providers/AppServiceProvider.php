<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->registerAuthorMacro();
    }

    /**
     * Adds a `author()` blueprint helper that stamps the standard authorship
     * columns (creator and last editor) on a table.
     */
    private function registerAuthorMacro(): void
    {
        Blueprint::macro('author', function (): void {
            /** @var Blueprint $this */
            $this->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $this->text('created_by_name')->nullable();
            $this->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $this->text('updated_by_name')->nullable();
        });
    }
}
