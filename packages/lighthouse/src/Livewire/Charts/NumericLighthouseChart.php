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
    public int $lighthouseSiteId = 0;

    public int $height = 200;

    #[Locked]
    public string $audit = '';

    public function mount(array $data): void
    {
        Validator::make($data, [
            'lighthouseSiteId' => 'required',
        ])->validate();

        $this->lighthouseSiteId = $data['lighthouseSiteId'];
    }

    public function data(): array
    {
        $resultIds = LighthouseResult::query()
            ->select(['id'])
            ->where('lighthouse_site_id', '=', $this->lighthouseSiteId)
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
            'millisecond' => fn (mixed $value) => round($value / 1000, 1),
            default => fn (mixed $value) => $value,
        };

        return [
            'type' => 'line',
            'data' => [
                'labels' => $audits->map(fn (LighthouseResultAudit $audit) => $audit->created_at->toDateTimeString('minute'))->toArray(),
                'datasets' => [
                    [
                        'label' => $title.' (s)',
                        'data' => $audits->map(fn (LighthouseResultAudit $audit) => $formatter($audit['numericValue']))->toArray(),
                        'pointRadius' => 0,
                        'pointHoverRadius' => 0,
                        'borderWidth' => 2,
                        'borderColor' => '#205EA6',
                        'tension' => 0.4,
                    ],
                ],
            ],
            'options' => [
                'plugins' => [
                    'legend' => [
                        'display' => true,
                    ],
                    'tooltip' => [
                        'enabled' => true,
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
        return Str::slug(get_class($this)).$this->lighthouseSiteId.$this->audit;
    }
}
