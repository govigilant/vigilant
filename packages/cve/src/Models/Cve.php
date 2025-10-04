<?php

namespace Vigilant\Cve\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $identifier
 * @property ?float $score
 * @property string $description
 * @property Carbon $published_at
 * @property Carbon $modified_at
 * @property array $data
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Collection<int, CveMonitorMatch> $matches
 */
class Cve extends Model
{
    protected $guarded = [];

    protected $casts = [
        'score' => 'float',
        'published_at' => 'datetime',
        'modified_at' => 'datetime',
        'data' => 'array',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(CveMonitorMatch::class);
    }
}
