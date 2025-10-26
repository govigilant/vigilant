<?php

namespace Vigilant\Frontend\Integrations\Table;

use Closure;
use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Columns\BaseColumn;

class ChartColumn extends BaseColumn
{
    public string $component = '';

    public Closure $parameterCallback;

    public function component(string $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function parameters(Closure $parameterCallback): static
    {
        $this->parameterCallback = $parameterCallback;

        return $this;
    }

    public function render(Model $model): mixed
    {
        return view('frontend::integrations.table.chart-column', [
            'component' => $this->component,
            'parameters' => ($this->parameterCallback)($model),
        ]);
    }
}
