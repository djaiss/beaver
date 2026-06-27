<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\DocNavigationBuilder;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.docs', function (\Illuminate\View\View $view): void {
            $version = request()->route('version') ?? config('docs.default_version');
            $view->with('docNav', (new DocNavigationBuilder)->build($version));
            $view->with('currentVersion', $version);
        });
    }
}
