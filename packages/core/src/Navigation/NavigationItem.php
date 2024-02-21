<?php

namespace Vigilant\Core\Navigation;

use Closure;

class NavigationItem
{
    protected ?Closure $childrenCallback;

    public function __construct(
        public string $name,
        public string $url,
        public ?string $icon = null,
        public int $sort = 0,
    ) {
    }

    public function active(): bool
    {
        return request()->url() === $this->url;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function sort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function children(Closure $children): static
    {
        $this->childrenCallback = $children;

        return $this;
    }

    public function hasChildren(): bool
    {
        return isset($this->childrenCallback);
    }

    public function getChildren(): array
    {
        $navigation = new Navigation();

        call_user_func($this->childrenCallback, $navigation);

        return $navigation->items();
    }
}