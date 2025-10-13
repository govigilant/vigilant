<?php

namespace Vigilant\Uptime\Notifications\Conditions;

use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Uptime\Notifications\LatencyChangedNotification;
use Vigilant\Uptime\Notifications\LatencyPeakNotification;

class LatencyMsCondition extends Condition
{
    public static string $name = 'Latency (ms)';

    public ConditionType $type = ConditionType::Number;

    public function operands(): array
    {
        return [
            'current' => 'Current',
            'change' => 'Change',
            'change_absolute' => 'Change (absolute)',
        ];
    }

    public function operators(): array
    {
        return [
            '=' => 'Equal to',
            '<>' => 'Not equal to',
            '<' => 'Less than',
            '<=' => 'Less or equal than',
            '>' => 'Greater than',
            '>=' => 'Greater or equal than',
        ];
    }

    public function applies(
        Notification $notification,
        ?string $operand,
        ?string $operator,
        mixed $value,
        ?array $meta
    ): bool {
        /** @var LatencyChangedNotification|LatencyPeakNotification $notification */
        
        $msValue = match ($operand) {
            'current' => $this->getCurrentLatency($notification),
            'change' => $this->getLatencyChange($notification),
            'change_absolute' => abs($this->getLatencyChange($notification)),
            default => 0,
        };

        return match ($operator) {
            '=' => $msValue == $value,
            '<>' => $msValue != $value,
            '<' => $msValue < $value,
            '<=' => $msValue <= $value,
            '>' => $msValue > $value,
            '>=' => $msValue >= $value,
            default => false,
        };
    }

    protected function getCurrentLatency(LatencyChangedNotification|LatencyPeakNotification $notification): float
    {
        if ($notification instanceof LatencyChangedNotification) {
            return $notification->currentAverage;
        }
        
        if ($notification instanceof LatencyPeakNotification) {
            return $notification->peakLatency;
        }

        return 0;
    }

    protected function getLatencyChange(LatencyChangedNotification|LatencyPeakNotification $notification): float
    {
        if ($notification instanceof LatencyChangedNotification) {
            return $notification->currentAverage - $notification->previousAverage;
        }
        
        if ($notification instanceof LatencyPeakNotification) {
            return $notification->peakLatency - $notification->averageLatency;
        }

        return 0;
    }
}
