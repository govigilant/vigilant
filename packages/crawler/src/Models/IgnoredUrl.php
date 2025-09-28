<?php

namespace Vigilant\Crawler\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vigilant\Crawler\Observers\IgnoredUrlObserver;

/**
 * @property int $id
 * @property int $crawler_id
 * @property string $url_hash
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Crawler $crawler
 */
#[ObservedBy(IgnoredUrlObserver::class)]
class IgnoredUrl extends Model
{
    protected $table = 'web_crawler_ignored_urls';

    protected $guarded = [];

    public function crawler(): BelongsTo
    {
        return $this->belongsTo(Crawler::class);
    }
}
