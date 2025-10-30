<?php

declare(strict_types=1);

namespace Vigilant\Frontend\Integrations\Table;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;

abstract class BaseTable extends LivewireTable
{
    protected bool $useNavigate = true;
}
