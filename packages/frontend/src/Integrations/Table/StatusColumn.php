<?php

namespace Vigilant\Frontend\Integrations\Table;

use Closure;
use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Columns\BaseColumn;

/**
 * @property view-string $view
 */
class StatusColumn extends BaseColumn
{
    protected string $view = 'frontend::integrations.table.status-column';

    public Closure $statusCallback;

    public Closure $textCallback;

    public function status(Closure $statusCallback): static
    {
        $this->statusCallback = $statusCallback;

        return $this;
    }

    public function text(Closure $textCallback): static
    {
        $this->textCallback = $textCallback;

        return $this;
    }

    public function render(Model $model): mixed
    {
        return view($this->view, [
            'text' => ($this->textCallback)($model),
            'status' => ($this->statusCallback)($model),
        ]);
    }
}
