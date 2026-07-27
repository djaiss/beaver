<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MarketingLayout extends Component
{
    /**
     * Both the title and the description are optional. A page that gives neither
     * is described by App\Services\MarketingSeo from its route name, which is
     * where the copy for the public pages lives.
     */
    public function __construct(
        public array $breadcrumbItems = [],
        public ?string $title = null,
        public ?string $description = null,
    ) {}

    public function render(): View
    {
        return view('layouts.marketing', [
            'breadcrumbItems' => $this->breadcrumbItems,
            'pageTitle' => $this->title,
            'pageDescription' => $this->description,
        ]);
    }
}
