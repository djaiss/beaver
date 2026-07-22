<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function __construct(
        public string $title = '',
        public ?Collection $collection = null,
    ) {}

    /**
     * The categories of the collection, for the sidebar. Names are encrypted, so
     * the sort happens in memory.
     *
     * @return EloquentCollection<int, Category>
     */
    public function categories(): EloquentCollection
    {
        if ($this->collection === null) {
            return new EloquentCollection;
        }

        return $this->collection->categories()
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
