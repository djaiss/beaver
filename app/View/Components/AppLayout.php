<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Vault;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function __construct(
        public string $title = '',
        public ?Vault $vault = null,
    ) {}

    public function render(): View
    {
        return view('layouts.app', [
            'vault' => $this->vault,
        ]);
    }
}
