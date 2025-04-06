<?php

namespace Vigilant\Settings\Livewire\Tabs;

use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Settings\Livewire\Forms\ProfileForm;
use Vigilant\Users\Models\User;

class Profile extends Component
{
    use DisplaysAlerts;

    public ProfileForm $form;

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->form->fill($user->toArray());
    }

    public function save(): void
    {
        $this->validate();

        /** @var User $user */
        $user = auth()->user();

        $validated = $this->form->validate();
        $user->update($validated);

        $this->alert(
            __('Saved'),
            __('Profile information saved'),
            AlertType::Success
        );
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'settings::tabs.profile';

        return view($view);
    }
}
