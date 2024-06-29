<?php

namespace Vigilant\Notifications\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Vigilant\Notifications\Models\Trigger;

class RenameConditionClassesCommand extends Command
{
    protected $signature = 'notifications:rename-classes';

    protected $description = 'Rename moved classes';

    public function handle(): int
    {
        /** @var array<string, string> $renamed */
        $renamed = config('notifications.moved_conditions');

        foreach ($renamed as $old => $new) {
            $this->info("Checking $old => $new");

            $oldClass = Str::replace('\\', '\\\\\\\\', $old);

            $triggers = Trigger::query()
                ->withoutGlobalScopes()
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(conditions, '$')) LIKE ?", ["%$oldClass%"])
                ->get();

            $this->info("Found {$triggers->count()} triggers to update");

            foreach ($triggers as $trigger) {
                $trigger->update([
                    'conditions' => $this->processGroup($trigger->conditions, $old, $new)
                ]);
            }

            $this->newLine();
        }

        return static::SUCCESS;
    }

    protected function processGroup(array $group, string $old, string $new): array
    {
        foreach ($group['children'] ?? [] as $index => $child) {
            if ($child['type'] === 'condition') {
                if ($child['condition'] === $old) {
                    data_set($group, "children.$index.condition", $new);
                }
            }

            if ($child['type'] === 'group') {
                data_set($group, "children.$index", $this->processGroup($child, $old, $new));
            }
        }

        return $group;
    }
}
