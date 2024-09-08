<?php

namespace Vigilant\Crawler\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Crawler\Enums\State;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property ?int $site_id
 * @property int $team_id
 * @property State $state
 * @property string $start_url
 * @property ?array $sitemaps
 * @property ?array $crawler_stats
 * @property array $settings
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Site $site
 * @property Collection<int, CrawledUrl> $urls
 */
#[ObservedBy([TeamObserver::class])]
#[ScopedBy(TeamScope::class)]
class Crawler extends Model
{
    protected $table = 'web_crawlers';

    protected $guarded = [];

    protected $casts = [
        'state' => State::class,
        'crawler_stats' => 'array',
        'sitemaps' => 'array',
        'settings' => 'array',
    ];

    public function totalUrlCount(): int
    {
        return $this->crawler_stats === null
            ? $this->urls()->count()
            : $this->crawler_stats['total_url_count'] ?? 0;
    }

    public function issueCount(): ?int
    {
        return $this->crawler_stats === null
            ? null
            : $this->crawler_stats['issue_count'] ?? 0;
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function urls(): HasMany
    {
        return $this->hasMany(CrawledUrl::class, 'crawler_id', 'id');
    }
}
