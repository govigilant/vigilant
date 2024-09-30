<?php

namespace Vigilant\OnBoarding\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Core\Services\TeamService;
use Vigilant\OnBoarding\Models\OnboardingStep;
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
        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);

        OnboardingStep::query()->updateOrCreate([
            'team_id' => $teamService->team()->id,
        ], [
            'finished_at' => now()
        ]);

        /** @var Site $site */
        $site = Site::query()->findOrFail($id);

        $this->redirectRoute('site.edit', ['site' => $site]);
    }

    public function render(): mixed
    {
        return view('onboarding::setup');
    }
}
