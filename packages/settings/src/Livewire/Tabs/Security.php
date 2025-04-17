<?php

namespace Vigilant\Settings\Livewire\Tabs;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Settings\Livewire\Forms\UpdatePasswordForm;
use Vigilant\Users\Models\User;

class Security extends Component
{
    use DisplaysAlerts;

    public UpdatePasswordForm $password;

    public function updatePassword(): void
    {
        $validated = $this->password->validate();

        /** @var User $user */
        $user = auth()->user();

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        $this->password->reset();

        $this->alertBrowser(
            __('Saved'),
            __('Password changed'),
            AlertType::Success
        );
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'settings::tabs.security';

        return view($view);
    }
}
