<?php

namespace Vigilant\Certificates\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Gate;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Certificates\Jobs\CheckCertificateJob;
use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Frontend\Integrations\Table\DateColumn;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class CertificateMonitorsTable extends LivewireTable
{
    protected string $model = CertificateMonitor::class;

    protected function columns(): array
    {
        return [
            Column::make(__('Domain'), 'domain')
                ->sortable()
                ->searchable(),

            Column::make(__('Port'), 'port')
                ->sortable()
                ->searchable(),

            Column::make(__('Protocol'), 'protocol')
                ->sortable()
                ->searchable(),

            DateColumn::make(__('Valid From'), 'valid_from')
                ->sortable()
                ->searchable(),

            DateColumn::make(__('Valid To'), 'valid_to')
                ->sortable()
                ->searchable(),

        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Check Now'), 'run', function (Enumerable $models): void {
                $models->each(fn (CertificateMonitor $monitor) => CheckCertificateJob::dispatch($monitor));
            }),

            Action::make(__('Enable'), 'enable', function (Enumerable $models): void {
                foreach ($models as $model) {
                    if (! Gate::allows('create', $model)) {
                        break;
                    }
                    $model->update(['enabled' => true]);

                }
            }),

            Action::make(__('Disable'), 'disable', function (Enumerable $models): void {
                $models->each(fn (LighthouseMonitor $monitor) => $monitor->update(['enabled' => false]));
            }),

            Action::make(__('Delete'), 'delete', function (Enumerable $models): void {
                $models->each(fn (CertificateMonitor $monitor): ?bool => $monitor->delete());
            }),
        ];
    }

    protected function link(Model $model): ?string
    {
        return route('certificates.index', ['monitor' => $model]);
    }
}
