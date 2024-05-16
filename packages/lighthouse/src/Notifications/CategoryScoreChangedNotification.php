<?php

namespace Vigilant\Lighthouse\Notifications;

use Vigilant\Lighthouse\Data\CategoryResultDifferenceData;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Notifications\Conditions\AverageScoreCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class CategoryScoreChangedNotification extends Notification implements HasSite
{
    public static string $name = 'Lighthouse score changed';

    public static array $defaultConditions = [
        'type' => 'group',
        'children' => [
            [
                'type' => 'condition',
                'condition' => AverageScoreCondition::class,
                'operator' => '>=',
                'operand' => 'absolute',
                'value' => 10,
            ],
        ],
    ];

    public function __construct(
        public LighthouseResult $result,
        public CategoryResultDifferenceData $data
    ) {
    }

    public function title(): string
    {
        return __('Lighthouse score changed');
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
        return $this->result->lighthouseSite?->site;
    }
}
