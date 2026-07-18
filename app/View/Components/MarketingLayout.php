<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MarketingLayout extends Component
{
    /**
     * @param  array<int, mixed>  $breadcrumbItems
     * @param  ?array{version: string, text: string}  $announcement
     * @param  array<int, mixed>  $footerColumns
     */
    public function __construct(
        public array $breadcrumbItems = [],
        public ?array $announcement = null,
        public array $footerColumns = [],
    ) {}

    public function render(): View
    {
        return view('layouts.marketing', [
            'breadcrumbItems' => $this->breadcrumbItems,
            'announcement' => $this->announcement,
            'footerColumns' => $this->footerColumns,
        ]);
    }
}
