<?php

namespace Vigilant\OnBoarding\Livewire;

use Livewire\Component;
use Vigilant\OnBoarding\Models\OnboardingStep;
use Vigilant\Users\Models\User;

class Complete extends Component
{
    public function finish(): void
    {
        /** @var User $user */
        $user = auth()->user();

        OnboardingStep::query()->updateOrCreate(
            ['team_id' => $user->currentTeam?->id],
            ['step' => 'complete', 'finished_at' => now()]
        );

        $this->redirectRoute('sites');
    }

    public function goBack(): void
    {
        $this->redirectRoute('onboard.notifications');
    }

    public function checkStepFinished(): void
    {
        // This is the final step, no redirect needed
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'onboarding::complete';

        return view($view);
    }
}
