<?php

namespace Vigilant\Cve\Notifications;

use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Cve\Notifications\Conditions\ScoreCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class CveMatchedNotification extends Notification implements HasSite
{
    public static string $name = 'CVE matched monitored keyword';

    public static ?int $defaultCooldown = 60;

    public static array $defaultConditions = [
        'type' => 'group',
        'children' => [
            [
                'type' => 'condition',
                'condition' => ScoreCondition::class,
                'operator' => '>=',
                'value' => 6,
            ],
        ],
    ];

    public function __construct(public CveMonitor $monitor, public Cve $cve) {}

    public function title(): string
    {
        return __(':id with a score of :score found', [
            'id' => $this->cve->identifier,
            'score' => $this->cve->score ?? 0,
        ]);
    }

    public function description(): string
    {
        $description = __('The CVE :id was published at :publishedAt and has a score of :score and matched your monitored keyword :keyword.', [
            'id' => $this->cve->identifier,
            'publishedAt' => $this->cve->published_at->toDateString(),
            'keyword' => $this->monitor->keyword,
            'score' => $this->cve->score ?? 0,
        ]);

        $description .= "\n\n";
        $description .= __('Description: :description', [
            'description' => str($this->cve->description)->limit(500),
        ]);

        return $description;
    }

    public function level(): Level
    {
        if ($this->cve->cvss_score >= 7) {
            return Level::Critical;
        }

        if ($this->cve->cvss_score >= 4) {
            return Level::Warning;
        }

        return Level::Info;
    }

    public function uniqueId(): string|int
    {
        return $this->cve->id;
    }

    public function site(): ?Site
    {
        return $this->monitor->site;
    }
}
