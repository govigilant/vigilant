<?php

namespace Vigilant\Settings\Livewire;

use Illuminate\Support\Arr;
use Laravel\Jetstream\Jetstream;
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
        $tabs = [];

        $tabs['profile'] = [
            'title' => 'Profile',
            'component' => 'settings-tab-profile',
        ];

        $tabs['security'] = [
            'title' => 'Account Security',
            'component' => 'settings-tab-security',
        ];

        if (Jetstream::hasTeamFeatures()) {
            $tabs['team'] = [
                'title' => 'Team Settings',
                'component' => 'settings-tab-team',
            ];
        }

        if (! ce()) {
            $tabs['billing'] = [
                'title' => 'Billing',
                'component' => 'settings-tab-billing',
            ];
        }

        return $tabs;
    }

    public function render(): mixed
    {

        /** @var view-string $view */
        $view = 'settings::index';

        return view($view, [
            'tabs' => $this->tabs(),
            'tab' => $this->tab,
        ]);
    }
}
