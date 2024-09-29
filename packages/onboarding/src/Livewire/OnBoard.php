<?php

namespace Vigilant\OnBoarding\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Sites\Models\Site;

class OnBoard extends Component
{
    public function save(): void
    {
        $this->dispatch('save');
    }

    #[On('siteSaved')]
    public function siteSaved(int $id): void
    {
        /** @var Site $site */
        $site = Site::query()->findOrFail($id);

        $this->redirectRoute('site.edit', ['site' => $site]);
    }

    public function render(): mixed
    {
        return view('onboarding::setup');
    }
}
