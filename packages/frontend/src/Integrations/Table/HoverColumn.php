<?php

namespace Vigilant\Frontend\Integrations\Table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RamonRietdijk\LivewireTables\Columns\BaseColumn;

class HoverColumn extends BaseColumn
{
    protected string $view = 'frontend::integrations.table.hover-column';

    protected int $maxLength = 60;

    public function length(int $length): static
    {
        $this->maxLength = $length;

        return $this;
    }

    public function render(Model $model): mixed
    {
        $value = $this->resolveValue($model);

        return view($this->view, [
            'value' => $value,
            'preview' => strip_tags(Str::limit($value, $this->maxLength)),
            'raw' => $this->raw,
        ]);
    }
}
