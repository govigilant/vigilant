<?php

namespace Vigilant\OnBoarding\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\OnBoarding\Models\OnboardingStep;
use Vigilant\Users\Models\User;

class NotificationChannel extends Component
{
    #[On('onboard.notifications')]
    public function redirectNextStep(): void
    {
        /** @var User $user */
        $user = auth()->user();

        OnboardingStep::query()->updateOrCreate(
            ['team_id' => $user->currentTeam?->id],
            ['step' => 'notification-channel', 'finished_at' => now()]
        );

        $this->redirectRoute('sites');
    }

    public function checkStepFinished(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $onBoard = OnboardingStep::query()
            ->where('team_id', '=', $user->currentTeam?->id)
            ->where('step', '=', 'notification-channel')
            ->first();

        if ($onBoard !== null && $onBoard->finished_at !== null) {
            $this->redirectNextStep();
        }
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'onboarding::notification-channel';

        return view($view);
    }
}
