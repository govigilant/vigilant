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
            'team' => [
                'title' => 'Team',
                'component' => 'settings-tab-team',
            ],
            //[
            //    'title' => 'Data Retention',
            //],
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
