<?php

namespace Vigilant\Crawler\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Vigilant\Crawler\Observers\IgnoredUrlObserver;

/**
 * @property int $id
 * @property int $crawler_id
 * @property string $url_hash
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Crawler $crawler
 */
#[ObservedBy(IgnoredUrlObserver::class)]
class IgnoredUrl extends Model
{
    protected $table = 'web_crawler_ignored_urls';

    protected $guarded = [];

    public function url(): HasOne
    {
        return $this->hasOne(CrawledUrl::class, 'url_hash', 'url_hash')
            ->where('crawler_id', '=', $this->crawler_id);
    }

    public function crawler(): BelongsTo
    {
        return $this->belongsTo(Crawler::class);
    }
}
