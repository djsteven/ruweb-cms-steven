<?php

namespace App\View\Components;

use App\Models\Menu;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class MenuComponent extends Component
{
    public Collection $items;

    public function __construct(string $slug = 'header', ?string $location = null)
    {
        $menu = $location
            ? Menu::findByLocation($location)
            : Menu::findBySlug($slug);

        $this->items = $menu ? $menu->tree() : collect();

        if (($location === 'header' || ($location === null && $slug === 'header'))) {
            $this->items = $this->items
                ->reject(fn ($item) => strcasecmp(trim((string) $item->label), 'Home') === 0)
                ->values();
        }
    }

    public function render()
    {
        return view('components.menu');
    }
}
