<?php

namespace Vigilant\Settings\Livewire;

use Illuminate\Support\Arr;
use Livewire\Attributes\Url;
use Livewire\Component;

class Settings extends Component
{
    #[Url]
    public string $tab = '';

    public function mount(): void
    {
        if (blank($this->tab)) {
            /** @var string $tab */
            $tab = Arr::first(array_keys($this->tabs()));
            $this->tab = $tab;
        }
    }

    protected function tabs(): array
    {
        return [
            'profile' => [
                'title' => 'Profile',
                'component' => 'settings-tab-profile',
            ],
            'security' => [
                'title' => 'Account Security',
                'component' => 'settings-tab-security',
            ],
            'team' => [
                'title' => 'Team Settings',
                'component' => 'settings-tab-team',
            ],
        ];
    }

    public function render(): mixed
    {

        return view('settings::index', [
            'tabs' => $this->tabs(),
            'tab' => $this->tab,
        ]);
    }
}
