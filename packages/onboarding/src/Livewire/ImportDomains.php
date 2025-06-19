<?php

namespace Vigilant\OnBoarding\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\OnBoarding\Models\OnboardingStep;
use Vigilant\Users\Models\User;

class ImportDomains extends Component
{
    #[On('sites-imported')]
    public function redirectNextStep(): void
    {
        /** @var User $user */
        $user = auth()->user();

        OnboardingStep::query()->updateOrCreate(
            ['team_id' => $user->currentTeam?->id],
            ['step' => 'domain-import', 'finished_at' => now()]
        );

        $this->redirectRoute('onboard.notifications');
    }

    public function checkStepFinished(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $onBoard = OnboardingStep::query()
            ->where('team_id', '=', $user->currentTeam?->id)
            ->where('step', '=', 'domain-import')
            ->first();

        if ($onBoard !== null && $onBoard->finished_at !== null) {
            $this->redirectNextStep();
        }
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'onboarding::import-domains';

        return view($view, [
            'name' => ucfirst(auth()->user()->name ?? 'User'),
        ]);
    }
}
