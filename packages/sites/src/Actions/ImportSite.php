<?php

namespace Vigilant\Sites\Actions;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Core\Services\TeamService;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Dns\Client\DnsClient;
use Vigilant\Dns\Enums\Type as DnsType;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Users\Models\User;

class ImportSite
{
    public function __construct(
        protected TeamService $teamService,
        protected DnsClient $dnsClient
    ) {}

    /** @param array<string, bool> $monitors */
    public function import(int $teamId, string $domain, array $monitors): void
    {
        $this->teamService->setTeamById($teamId);
        $user = User::query()->firstWhere('current_team_id', $teamId);

        throw_if($user === null, 'User not found');

        if (Gate::forUser($user)->denies('create', Site::class)) {
            return;
        }

        $site = Site::query()->firstOrCreate(['url' => 'https://'.$domain]);

        if ($monitors['uptime'] ?? false) {
            $this->importUptime($site);
        }
        if ($monitors['lighthouse'] ?? false) {
            $this->importLighthouse($site);
        }
        if ($monitors['dns'] ?? false) {
            $this->importDns($site);
        }
        if ($monitors['certificate'] ?? false) {
            $this->importCertificate($site);
        }
        if ($monitors['crawler'] ?? false) {
            $this->importCrawler($site);
        }
    }

    protected function importUptime(Site $site): void
    {
        Monitor::query()->firstOrCreate([
            'site_id' => $site->id,
            'team_id' => $site->team_id,
        ], [
            'name' => $site->url,
            'enabled' => true,
            'type' => Type::Http,
            'interval' => 60,
            'retries' => 1,
            'timeout' => 5,
            'settings' => [
                'host' => $site->url,
            ],
        ]);

    }

    protected function importLighthouse(Site $site): void
    {
        $intervals = array_keys(config()->array('lighthouse.intervals'));

        LighthouseMonitor::query()->firstOrCreate([
            'site_id' => $site->id,
            'team_id' => $site->team_id,
        ], [
            'url' => $site->url,
            'interval' => Arr::first($intervals),
            'settings' => [],
        ]);
    }

    protected function importDns(Site $site): void
    {
        /** @var array<int, AbstractRecord> $records */
        $records = $this->dnsClient->get(str_replace('https://', '', $site->url), [
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

            $type = DnsType::tryFrom($data['type']);

            if ($type === null) {
                continue;
            }

            $value = $type->parser()->parse($data);

            DnsMonitor::query()->updateOrCreate([
                'site_id' => $site->id,
                'team_id' => $site->team_id,
                'type' => $type,
                'record' => $record->getHost(),
            ], [
                'value' => $value,
            ]);
        }
    }

    protected function importCertificate(Site $site): void
    {
        CertificateMonitor::query()->firstOrCreate([
            'site_id' => $site->id,
            'team_id' => $site->team_id,
        ], [
            'domain' => $site->url,
            'port' => 443,
        ]);
    }

    protected function importCrawler(Site $site): void
    {
        Crawler::query()->firstOrCreate([
            'site_id' => $site->id,
            'team_id' => $site->team_id,
        ], [
            'state' => State::Pending,
            'schedule' => '0 10 * * 1',
            'start_url' => $site->url,
        ]);
    }
}
