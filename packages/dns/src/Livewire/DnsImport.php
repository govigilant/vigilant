<?php

namespace Vigilant\Dns\Livewire;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Dns\Client\DnsClient;
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
        if (count($this->deleted) === count($this->records)) {
            $this->records = [];
        }
    }

    #[On('save')]
    public function save(): void
    {
        $this->authorize('create', DnsMonitor::class);

        foreach ($this->records as $index => $record) {
            if (in_array($index, $this->deleted)) {
                if (array_key_exists('monitor_id', $record)) {
                    DnsMonitor::query()
                        ->where('id', '=', $record['monitor_id'])
                        ->delete();
                }

                continue;
            }

            DnsMonitor::query()->updateOrCreate([
                'site_id' => $this->siteId,
                'type' => $record['type'],
                'record' => $record['host'],
            ], [
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
        $this->records = [];

        $this->validate([
            'domain' => ['required', 'max:255', new Fqdn],
        ]);

        /** @var DnsClient $client */
        $client = app(DnsClient::class);

        /** @var array<int, AbstractRecord> $records */
        $records = $client->get($this->domain, [
            RecordTypes::A,
            RecordTypes::AAAA,
            RecordTypes::CNAME,
            RecordTypes::SOA,
            RecordTypes::TXT,
            RecordTypes::MX,
            RecordTypes::NS,
        ]);

        foreach ($records as $record) {
            $data = $record->toArray();

            $type = Type::tryFrom($data['type']);

            if ($type === null) {
                continue;
            }

            $value = $type->parser()->parse($data);

            $this->records[] = [
                'type' => $type,
                'host' => $data['host'],
                'value' => $value,
            ];
        }

        $this->noRecords = count($records) === 0;
        $this->deleted = [];
    }

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'dns::livewire.import';

        return view($view);
    }
}
