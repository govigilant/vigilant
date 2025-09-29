<?php

namespace Vigilant\Core\Navigation;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class NavigationItem
{
    public array $children = [];

    public function __construct(
        public string $name,
        public ?string $url,
        public ?string $icon = null,
        public int $sort = 0,
        public ?array $routeIs = null,
        public ?string $gate = null,
        public ?string $code = null,
        public ?string $parent = null
    ) {
        if ($code === null) {
            $this->code = str($name)
                ->slug()
                ->replace('-', '_')
                ->toString();
        }
    }

    public function active(): bool
    {
        if ($this->routeIs !== null) {
            return Route::is($this->routeIs);
        }

        return request()->url() === $this->url;
    }

    public function shouldRender(): bool
    {
        if ($this->gate === null) {
            return true;
        }

        return Gate::check($this->gate);
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function routeIs(string ...$routeIs): static
    {
        $this->routeIs = $routeIs;

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

    public function gate(string $gate): static
    {
        $this->gate = $gate;

        return $this;
    }

    public function sort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function code(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function parent(string $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    public function getChildren(): array
    {
        return $this->children;
    }
}
