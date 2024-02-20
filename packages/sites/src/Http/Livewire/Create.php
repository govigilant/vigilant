<?php

namespace Vigilant\Sites\Http\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Models\User;

class Create extends Component
{
    #[Validate('required|url')]
    public string $url = '';

    public function save(): void
    {
        $this->validate();

        /** @var User $user */
        $user = auth()->user();

        Site::query()->create([
            'team_id' => $user->currentTeam->id,
            'url' => $this->url,
        ]);

        $this->redirectRoute('sites');
    }

    public function render(): View
    {
        return view('sites::livewire.create');
    }
}
