<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Catalog;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function __construct(
        public string $title = '',
        public ?Catalog $catalog = null,
    ) {}

    /**
     * The categories of the collection, for the sidebar. Names are encrypted, so
     * the sort happens in memory.
     *
     * @return EloquentCollection<int, Category>
     */
    public function categories(): EloquentCollection
    {
        if ($this->catalog === null) {
            return new EloquentCollection;
        }

        return $this->catalog->categories()
            ->withCount('items')
            ->get()
            ->sortBy('name')
            ->values();
    }

    public function render(): View
    {
        return view('layouts.app');
    }
}
