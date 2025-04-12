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
use Vigilant\Frontend\Integrations\Table\Enums\Status;
use Vigilant\Frontend\Integrations\Table\StatusColumn;
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

            StatusColumn::make(__('Expires In'), 'valid_to', 'expires_in')
                ->sortable()
                ->text(function (CertificateMonitor $monitor): string {
                    if (! $monitor->enabled) {
                        return __('Disabled');
                    }

                    if ($monitor->valid_to === null) {
                        return __('Unknown');
                    }

                    if ($monitor->valid_to->isPast()) {
                        return __('Expired');
                    }

                    return $monitor->valid_to->longRelativeDiffForHumans();

                })
                ->status(function (CertificateMonitor $monitor): Status {
                    if (! $monitor->enabled) {
                        return Status::Danger;
                    }

                    if ($monitor->valid_to === null) {
                        return Status::Danger;
                    }

                    if ($monitor->valid_to->isPast()) {
                        return Status::Danger;
                    }

                    if ($monitor->valid_to->greaterThan(now()->addWeek())) {
                        return Status::Success;
                    }

                    return Status::Warning;
                }),

            Column::make(__('Issuer'))
                ->displayUsing(function (CertificateMonitor $monitor): string {
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
