<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MarketingLayout extends Component
{
    public function __construct(
        public array $breadcrumbItems = [],
        public ?string $title = null,
    ) {}

    public function render(): View
    {
        return view('layouts.marketing', [
            'breadcrumbItems' => $this->breadcrumbItems,
            'title' => $this->title ? $this->title.' · '.config('app.name') : null,
        ]);
    }
}
