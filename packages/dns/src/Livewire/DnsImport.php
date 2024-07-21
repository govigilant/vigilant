<?php

namespace Vigilant\Dns\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Dns\Enums\Type;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Frontend\Traits\CanBeInline;
use Vigilant\Frontend\Validation\Fqdn;
use Vigilant\Sites\Models\Site;

class DnsImport extends Component
{
    use CanBeInline;
    use DisplaysAlerts;

    #[Locked]
    public ?int $siteId = null;

    public string $domain = '';

    public array $records = [];

    public array $deleted = [];

    public bool $noRecords = false;

    public function mount(?int $siteId = null): void
    {
        $this->siteId = $siteId;

        if ($siteId !== null) {
            /** @var Site $site */
            $site = Site::query()->findOrFail($siteId);

            $this->records = $site->dnsMonitors->map(function (DnsMonitor $monitor): array {
                return [
                    'monitor_id' => $monitor->id,
                    'type' => $monitor->type,
                    'host' => $monitor->record,
                    'value' => $monitor->value,
                ];
            })->toArray();

            $this->domain = Str::of($site->url)->replace(['https://', 'http://'], '')->before('/')->value();

        }
    }

    public function remove(int $index): void
    {
        $this->deleted[] = $index;
    }

    #[On('save')]
    public function save(): void
    {
        foreach ($this->records as $index => $record) {
            if (in_array($index, $this->deleted)) {
                if (array_key_exists('monitor_id', $record)) {
                    DnsMonitor::query()
                        ->where('id', '=', $record['monitor_id'])
                        ->delete();
                }

                continue;
            }

            DnsMonitor::query()->firstOrCreate([
                'site_id' => $this->siteId,
                'type' => $record['type'],
                'record' => $record['host'],
                'value' => $record['value'],
            ]);
        }

        if ($this->inline) {
            return;
        }

        $this->alert(
            __('Saved'),
            __('Selected records are being monitored'),
            AlertType::Success
        );
        $this->redirectRoute('dns.index');
    }

    public function lookup(): void
    {
        $this->validate([
            'domain' => ['required', 'max:255', new Fqdn],
        ]);

        /** @var array<int, array<string, mixed>> $records */
        $records = dns_get_record($this->domain, DNS_ALL);

        foreach ($records as $record) {
            $type = Type::tryFrom($record['type']);

            if ($type === null) {
                continue;
            }

            $value = $type->parser()->parse($record);

            $this->records[] = [
                'type' => $type,
                'host' => $record['host'],
                'value' => $value,
            ];
        }

        $this->noRecords = count($records) === 0;
        $this->deleted = [];
    }

    public function render(): View
    {
        return view('dns::livewire.import');
    }
}
