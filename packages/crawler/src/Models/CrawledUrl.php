<?php

namespace Vigilant\Crawler\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Crawler\Observers\CrawledUrlObserver;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Models\Team;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property string $uuid
 * @property ?int $crawler_id
 * @property int $team_id
 * @property int $found_on_id
 * @property bool $crawled
 * @property bool $ignored
 * @property string $url
 * @property string $url_hash
 * @property int $status
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Site $site
 * @property Crawler $crawler
 * @property ?CrawledUrl $foundOn
 * @property ?Team $team
 */
#[ObservedBy([TeamObserver::class, CrawledUrlObserver::class])]
#[ScopedBy(TeamScope::class)]
class CrawledUrl extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';

    protected $table = 'web_crawled_urls';

    protected $guarded = [];

    protected $casts = [
        'crawled' => 'bool',
        'ignored' => 'bool',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function crawler(): BelongsTo
    {
        return $this->belongsTo(Crawler::class);
    }

    public function foundOn(): BelongsTo
    {
        return $this->belongsTo(static::class, 'found_on_id', 'uuid');
    }

    public function hash(): void
    {
        if ($this->url_hash === null) {
            $this->url_hash = md5($this->url);
        }
    }
}
