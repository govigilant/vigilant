<?php

namespace Vigilant\Notifications\Http\Livewire\Notifications\Conditions;

use Illuminate\Support\Arr;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Notifications\Facades\NotificationRegistry;

class ConditionBuilder extends Component
{
    #[Locked]
    public string $notification;

    public array $parent = [
        'type' => 'group',
        'operator' => 'any',
    ];

    public array $children = [];

    public function mount(string $notification, array $initial = []): void
    {
        $this->notification = $notification;

        if ($initial === []) {
            return;
        }

        $this->parent['operator'] = $initial['operator'] ?? 'any';
        $this->children = $initial['children'] ?? [];
    }

    public array $selectedCondition = [];

    public function addCondition(string $path): void
    {
        $condition = $this->selectedCondition[md5($path)] ?? Arr::first($this->conditions());

        $this->addToPath($path, [
            'type' => 'condition',
            'condition' => $condition,
            'value' => null,
        ]);
    }

    public function addGroup(string $path): void
    {
        $this->addToPath($path, [
            'type' => 'group',
            'operator' => 'all',
            'children' => [],
        ]);
    }

    public function deletePath(string $path): void
    {
        Arr::forget($this->children, $path);
        $this->children = array_values($this->children);

        $this->updated();
    }

    protected function addToPath(string $path, array $item): void
    {
        if (blank($path)) {
            $this->children[] = $item;
        } else {
            $children = data_get($this->children, $path.'.children', []);
            $children[] = $item;
            data_set($this->children, $path.'.children', $children);
        }

        $this->updated();
    }

    public function updated(): void
    {
        $conditions = $this->parent;
        $conditions['children'] = $this->children;

        $this->dispatch('conditions-updated', $conditions);
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'notifications::livewire.notifications.condition-builder';

        return view($view, [
            'conditions' => $this->conditions(),
        ]);
    }

    protected function conditions(): array
    {
        return NotificationRegistry::conditions($this->notification);
    }
}
