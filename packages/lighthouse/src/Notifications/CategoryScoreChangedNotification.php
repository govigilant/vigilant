<?php

namespace Vigilant\Lighthouse\Notifications;

use Vigilant\Lighthouse\Data\CategoryResultDifferenceData;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AverageScoreCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class CategoryScoreChangedNotification extends Notification implements HasSite
{
    public static string $name = 'Lighthouse score changed';

    public Level $level = Level::Warning;

    public static array $defaultConditions = [
        'type' => 'group',
        'children' => [
            [
                'type' => 'condition',
                'condition' => AverageScoreCondition::class,
                'operator' => '>=',
                'operand' => 'absolute',
                'value' => 20,
            ],
        ],
    ];

    public function __construct(
        public LighthouseResult $result,
        public CategoryResultDifferenceData $data
    ) {}

    public function title(): string
    {
        return __('Average lighthouse score changed on :url', [
            'url' => $this->result->lighthouseSite->url,
        ]);
    }

    public function description(): string
    {
        $performanceOld = $this->data->performanceOld() * 100;
        $performanceNew = $this->data->performanceNew() * 100;

        $accessibilityOld = $this->data->accessibilityOld() * 100;
        $accessibilityNew = $this->data->accessibilityNew() * 100;

        $bestPracticesOld = $this->data->bestPracticesOld() * 100;
        $bestPracticesNew = $this->data->bestPracticesNew() * 100;

        $seoOld = $this->data->seoOld() * 100;
        $seoNew = $this->data->seoNew() * 100;

        return __('New values are: Performance :performance, Accessibility :accessibility, Best Practices :best_practices, SEO :seo',
            [
                'performance' => "$performanceOld% => $performanceNew%",
                'accessibility' => "$accessibilityOld% => $accessibilityNew%",
                'best_practices' => "$bestPracticesOld% => $bestPracticesNew%",
                'seo' => "$seoOld% => $seoNew%",
            ]
        );
    }

    public function level(): Level
    {
        return $this->mostlyNegative()
            ? Level::Warning
            : Level::Success;
    }

    protected function mostlyNegative(): bool
    {
        return $this->data->averageDifference() < 0;
    }

    public function uniqueId(): string|int
    {
        return $this->result->id;
    }

    public function site(): ?Site
    {
        return $this->result->lighthouseSite->site;
    }
}
