<?php

namespace Vigilant\Notifications\Conditions;

use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Notifications\Notifications\Notification;

class ConditionEngine
{
    public function checkGroup(Notification $notification, array $group, string $operator = 'any'): bool
    {
        $children = collect($group['children'] ?? []);

        if ($children->isEmpty()) {
            return true;
        }

        $applies = true;

        foreach ($children as $condition) {
            if ($condition['type'] == 'condition') {
                if (! NotificationRegistry::hasCondition($condition['condition'])) {
                    continue;
                }

                /** @var Condition $instance */
                $instance = app($condition['condition']);

                $applies = $instance->applies(
                    $notification,
                    $condition['operand'] ?? null,
                    $condition['operator'] ?? null,
                    $condition['value'] ?? null,
                    $condition['meta'] ?? null
                );
            }

            if ($condition['type'] == 'group') {
                $applies = $this->checkGroup($notification, $condition, $condition['operator']);
            }

            if ($operator === 'any' && $applies) {
                return true;
            }

            if ($operator === 'all' && ! $applies) {
                return false;
            }
        }

        return $applies;
    }
}
