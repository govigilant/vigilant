<?php

namespace Vigilant\Lighthouse\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Vigilant\Core\Scopes\TeamScope;
use Vigilant\Users\Observers\TeamObserver;

/**
 * @property int $id
 * @property int $lighthouse_result_id
 * @property int $team_id
 * @property string $audit
 * @property string $title
 * @property string $explanation
 * @property string $description
 * @property ?float $score
 * @property string $scoreDisplayMode
 * @property ?array $details
 * @property ?array $warnings
 * @property ?array $items
 * @property ?array $metricSavings
 * @property ?float $guidanceLevel
 * @property ?float $numericValue
 * @property ?string $numericUnit
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?LighthouseResult $lighthouseResult
 */
#[ObservedBy([TeamObserver::class])]
#[ScopedBy([TeamScope::class])]
class LighthouseResultAudit extends Model
{
    protected $guarded = [];

    protected $casts = [
        'details' => 'array',
        'warnings' => 'array',
        'items' => 'array',
        'metricSavings' => 'array',
    ];

    public function lighthouseResult(): BelongsTo
    {
        return $this->belongsTo(LighthouseResult::class);
    }
}
