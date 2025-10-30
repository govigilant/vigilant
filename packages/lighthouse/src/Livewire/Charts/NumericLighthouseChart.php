<?php

namespace Vigilant\Lighthouse\Livewire\Charts;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Vigilant\Frontend\Http\Livewire\BaseChart;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseResultAudit;

#[Lazy]
#[Isolate]
class NumericLighthouseChart extends BaseChart
{
    #[Locked]
    public int $lighthouseMonitorId = 0;

    public int $height = 200;

    #[Locked]
    public string $audit = '';

    public function mount(array $data): void
    {
        Validator::make($data, [
            'lighthouseMonitorId' => 'required',
        ])->validate();

        $this->lighthouseMonitorId = $data['lighthouseMonitorId'];
    }

    public function data(): array
    {
        $resultIds = LighthouseResult::query()
            ->select(['id'])
            ->where('lighthouse_monitor_id', '=', $this->lighthouseMonitorId)
            ->get()
            ->pluck('id');

        $audits = LighthouseResultAudit::query()
            ->whereIn('lighthouse_result_id', $resultIds)
            ->where('audit', '=', $this->audit)
            ->get();

        $audit = $audits->first();

        if ($audit === null) {
            return [];
        }

        $title = $audit['title'];
        $unit = $audit['numericUnit'];

        $formatter = match ($unit) {
            'millisecond' => fn (mixed $value): float => round($value / 1000, 1),
            default => fn (mixed $value): mixed => $value ?? '-',
        };

        $color = $this->getChartColor(0); // Use first color (blue)

        return [
            'type' => 'line',
            'data' => [
                'labels' => $audits->map(fn (LighthouseResultAudit $audit): string => $audit->created_at?->toDateTimeString('minute') ?? '-')->toArray(),
                'datasets' => [
                    $this->dataset([
                        'label' => $title,
                        'data' => $audits->map(fn (LighthouseResultAudit $audit): string => $formatter($audit['numericValue']))->toArray(),
                        'borderColor' => $color['border'],
                        'backgroundColor' => $color['bg'],
                        'fill' => true,
                        'unit' => 's',
                    ]),
                ],
            ],
            'options' => [
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'display' => true,
                        'min' => 0,
                    ],
                    'x' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getIdentifier(): string
    {
        return Str::slug(get_class($this)).$this->lighthouseMonitorId.$this->audit;
    }
}
