<?php

namespace Vigilant\Certificates\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Certificates\Models\CertificateMonitorHistory;
use Vigilant\Frontend\Integrations\Table\DateColumn;

class CertificateMonitorHistoryTable extends LivewireTable
{
    protected string $model = CertificateMonitorHistory::class;

    #[Locked]
    public int $monitorId;

    public function mount(int $monitorId): void
    {
        $this->monitorId = $monitorId;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Domain'), 'certificateMonitor.domain')
                ->sortable()
                ->searchable(),

            DateColumn::make(__('Changed At'), 'created_at')
                ->sortable(),

            Column::make(__('Issuer'))
                ->displayUsing(function (CertificateMonitorHistory $monitor): string {
                    return data_get($monitor->data ?? [], 'issuer.CN', __('Unknown'));
                }),

            DateColumn::make(__('Valid From'), 'valid_from')
                ->sortable()
                ->searchable(),

            DateColumn::make(__('Valid To'), 'valid_to')
                ->sortable()
                ->searchable(),

            Column::make(__('Protocol'), 'protocol')
                ->hide()
                ->sortable()
                ->searchable(),
        ];
    }

    protected function query(): Builder
    {
        return parent::query()
            ->where('certificate_monitor_id', '=', $this->monitorId);
    }
}
