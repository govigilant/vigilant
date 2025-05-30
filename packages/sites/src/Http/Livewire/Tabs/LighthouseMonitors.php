<?php

namespace Vigilant\Sites\Http\Livewire\Tabs;

use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Sites\Models\Site;

class LighthouseMonitors extends Component
{
    #[Locked]
    public int $siteId;

    public bool $enabled = false;

    /** @var array<int, array<string, string|int>> $monitors */
    #[Validate]
    public array $monitors = [];

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'monitors.*.id' => ['nullable'],
            'monitors.*.url' => ['required', 'url'],
            'monitors.*.interval' => ['required', 'in:'.implode(',', array_keys(config('lighthouse.intervals')))],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'monitors.*.url' => 'URL',
        ];
    }

    public function mount(Site $site): void
    {
        $this->siteId = $site->id;

        $this->monitors = $site->lighthouseMonitors->map(function (LighthouseMonitor $monitor): array {
            return [
                'id' => $monitor->id,
                'url' => $monitor->url,
                'interval' => $monitor->interval,
            ];
        })->toArray();
    }

    public function updated(): void
    {
        $this->validate();
    }

    #[On('save')]
    public function save(): void
    {
        abort_if(Gate::denies('use-lighthouse'), 403);

        $monitors = $this->validate()['monitors'] ?? [];

        foreach ($monitors as $monitor) {
            if (! blank($monitor['id'] ?? null)) {
                /** @var LighthouseMonitor $model */
                $model = LighthouseMonitor::query()->findOrFail($monitor['id']);
            } else {
                /** @var LighthouseMonitor $model */
                $model = LighthouseMonitor::query()->newModelInstance([
                    'site_id' => $this->siteId,
                    'settings' => [],
                ]);
            }

            $model->url = $monitor['url'];
            $model->interval = $monitor['interval'];

            $model->save();
        }
    }

    public function addPage(): void
    {
        /** @var Site $site */
        $site = Site::query()->findOrFail($this->siteId);

        $defaultInterval = collect(config('lighthouse.intervals'))->keys()->first() ?? 60 * 24; // @phpstan-ignore-line

        $this->monitors[] = [
            'url' => $site->url,
            'interval' => $defaultInterval,
        ];
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'sites::livewire.tabs.lighthouse-monitor';

        return view($view, [
            'monitors' => $this->monitors,
        ]);
    }
}
