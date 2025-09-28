<?php

namespace Vigilant\Frontend\Integrations\Table;

use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Columns\BaseColumn;

class ActionsColumn extends BaseColumn
{
    public array $actions = [];

    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function render(Model $model): mixed
    {
        return view('frontend::integrations.table.actions-column', [
            'actions' => $this->actions,
            'model' => $model,
            'column' => $this,
        ]);
    }
}
