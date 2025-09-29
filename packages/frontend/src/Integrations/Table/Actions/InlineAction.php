<?php

namespace Vigilant\Frontend\Integrations\Table\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Concerns\Makeable;

class InlineAction
{
    use Makeable;

    public ?Closure $visible = null;

    public function __construct(
        public string $code,
        public string $name,
        public string $icon
    ) {}

    public function visible(Closure $callback): static
    {
        $this->visible = $callback;

        return $this;
    }

    public function isVisible(Model $model): bool
    {
        if ($this->visible === null) {
            return true;
        }

        return ($this->visible)($model);
    }
}
