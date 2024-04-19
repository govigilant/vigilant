<?php

namespace Vigilant\Notifications\Http\Livewire\Notifications\Conditions;

use Illuminate\Support\Arr;
use Livewire\Component;

class ConditionBuilder extends Component
{
    public array $parent = [
        'type' => 'group',
        'operator' => 'any',
    ];

    public array $children = [];

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

    protected function addToPath(string $path, array $item): void
    {
        if (blank($path)) {
            $this->children[] = $item;
            return;
        }

        $children = data_get($this->children, $path.'.children', []);
        $children[] = $item;
        data_set($this->children, $path.'.children', $children);
    }

    public function updated(): void
    {
       $conditions = $this->parent;
       $conditions['children'] = $this->children;

       $this->dispatch('conditions-updated', $conditions);
    }

    public function render(): mixed
    {
        return view('notifications::livewire.notifications.condition-builder', [
            'conditions' => $this->conditions(),
        ]);
    }

    protected function conditions(): array
    {
        // TODO: Filter on notification type
        return \Vigilant\Notifications\Facades\NotificationRegistry::conditions();
    }

}
