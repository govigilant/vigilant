<?php

namespace Vigilant\Sites\Http\Livewire\Tabs;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Vigilant\Frontend\Validation\CronExpression;
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
            'monitors.*.interval' => ['required', new CronExpression],
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
        // @phpstan-ignore-next-line
        $this->monitors = $site->lighthouseMonitors->map(function (LighthouseMonitor $monitor): array {
            return [
                'id' => $monitor->id, // @phpstan-ignore-line
                'url' => $monitor->url, // @phpstan-ignore-line
                'interval' => $monitor->interval, // @phpstan-ignore-line
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

        $this->monitors[] = [
            'url' => $site->url,
            'interval' => '0 */3 * * *',
        ];
    }

    public function render(): mixed
    {
        return view('sites::livewire.tabs.lighthouse-monitor', [
            'monitors' => $this->monitors,
        ]);
    }
}
