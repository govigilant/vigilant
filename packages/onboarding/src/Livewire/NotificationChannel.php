<?php

namespace Vigilant\OnBoarding\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Notifications\Channels\MailChannel;
use Vigilant\Notifications\Models\Channel;
use Vigilant\OnBoarding\Models\OnboardingStep;
use Vigilant\Users\Models\User;

class NotificationChannel extends Component
{
    public Channel $channel;

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->channel = new Channel([
            'channel' => MailChannel::class,
            'name' => 'E-mail',
            'settings' => [
                'to' => $user->email,
            ],
        ]);
    }

    #[On('channel-saved')]
    public function redirectNextStep(): void
    {
        /** @var User $user */
        $user = auth()->user();

        OnboardingStep::query()->updateOrCreate(
            ['team_id' => $user->currentTeam?->id],
            ['step' => 'notification-channel', 'finished_at' => now()]
        );

        $this->redirectRoute('onboard.complete');
    }

    public function goBack(): void
    {
        /** @var User $user */
        $user = auth()->user();

        // Clear the current step so users can go back
        OnboardingStep::query()
            ->where('team_id', '=', $user->currentTeam?->id)
            ->where('step', '=', 'notification-channel')
            ->delete();

        $this->redirectRoute('onboard');
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
            $this->redirectRoute('onboard.complete');
        }
    }

    public function skipOnboarding(): void
    {
        /** @var User $user */
        $user = auth()->user();

        OnboardingStep::query()->updateOrCreate(
            ['team_id' => $user->currentTeam?->id],
            ['step' => 'complete', 'finished_at' => now()]
        );

        $this->redirectRoute('sites');
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'onboarding::notification-channel';

        return view($view);
    }
}
