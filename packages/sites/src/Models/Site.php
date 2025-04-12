<?php

namespace Vigilant\Sites\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Sites\Observers\SiteObserver;
use Vigilant\Uptime\Models\Monitor as UptimeMonitor;

/**
 * @property int $id
 * @property int $team_id
 * @property string $url
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?UptimeMonitor $uptimeMonitor
 * @property ?Crawler $crawler
 * @property Collection<int, LighthouseMonitor> $lighthouseMonitors
 * @property Collection<int, DnsMonitor> $dnsMonitors
 */
#[ObservedBy([SiteObserver::class])]
#[ScopedBy([TeamScope::class])]
class Site extends Model
{
    protected $guarded = [];

    public function uptimeMonitor(): HasOne
    {
        return $this->hasOne(UptimeMonitor::class);
    }

    public function lighthouseMonitors(): HasMany
    {
        return $this->hasMany(LighthouseMonitor::class);
    }

    public function dnsMonitors(): HasMany
    {
        return $this->hasMany(DnsMonitor::class);
    }

    public function crawler(): HasOne
    {
        return $this->hasOne(Crawler::class);
    }

    public function certificateMonitor(): HasOne
    {
        return $this->hasOne(CertificateMonitor::class);
    }
}
