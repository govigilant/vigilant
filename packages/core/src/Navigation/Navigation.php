<?php

namespace Vigilant\Core\Navigation;

class Navigation
{
    protected array $paths = [];

    protected bool $loaded = false;

    protected array $items = [];

    public function path(string $path): static
    {
        $this->paths[] = $path;

        return $this;
    }

    public function add(
        ?string $url,
        string $name,
    ): NavigationItem {
        $item = new NavigationItem($name, $url);

        $this->items[] = $item;

        return $item;
    }

    public function items(): array
    {
        if (! $this->loaded) {
            foreach ($this->paths as $path) {
                require $path;
            }

            $this->loaded = true;
        }

        return collect($this->items)
            ->map(function (NavigationItem $item) {

                if ($item->parent !== null) {
                    return null;
                }

                $children = collect($this->items)
                    ->filter(fn (NavigationItem $child) => $child->parent === $item->code)
                    ->sortBy('sort')
                    ->toArray();

                $item->children = $children;

                return $item;
            })
            ->whereNotNull()
            ->sortBy('sort')
            ->toArray();
    }
}
