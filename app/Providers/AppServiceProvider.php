<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\DocNavigationBuilder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->registerAuthorMacro();

        View::composer('layouts.docs', function (\Illuminate\View\View $view): void {
            $version = request()->route('version') ?? config('docs.default_version');
            $view->with('docNav', (new DocNavigationBuilder)->build($version));
            $view->with('currentVersion', $version);
        });
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
