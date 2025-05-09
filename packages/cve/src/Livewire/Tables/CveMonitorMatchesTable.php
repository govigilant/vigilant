<?php

namespace Vigilant\Cve\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Cve\Models\CveMonitorMatch;

class CveMonitorMatchesTable extends LivewireTable
{
    protected string $model = CveMonitorMatch::class;

    #[Locked]
    public CveMonitor $monitor;

    public function mount(CveMonitor $monitor): void
    {
        $this->monitor = $monitor;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('CVE'), 'cve.identifier')
                ->searchable()
                ->sortable(),

            Column::make(__('Score'), 'cve.score')
                ->searchable()
                ->sortable(),

            Column::make(__('Description'), 'cve.description')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function link(Model $record): string
    {
        /** @var CveMonitorMatch $record */
        return route('cve.view', ['monitor' => $record->cveMonitor, 'cve' => $record->cve]);
    }

    protected function query(): Builder
    {
        return parent::query()
            ->where('cve_monitor_id', '=', $this->monitor->id)
            ->with('cve');
    }
}
