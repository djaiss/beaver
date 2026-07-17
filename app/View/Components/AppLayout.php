<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function __construct(
        public string $title = '',
        public ?Collection $collection = null,
    ) {}

    public function render(): View
    {
        return view('layouts.app');
    }
}
