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
        string $url,
        string $name,
        string $icon,
        int $sort = 0
    ): static {
        $this->items[] = [
            'url' => $url,
            'name' => $name,
            'icon' => $icon,
            'sort' => $sort,
        ];

        return $this;
    }

    public function items(): array
    {
        if (! $this->loaded) {
            foreach ($this->paths as $path) {
                require $path;
            }
        }

        return collect($this->items)
            ->sortBy('sort')
            ->toArray();
    }
}
