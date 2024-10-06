<?php

namespace Vigilant\Settings\Livewire\Forms;

use Livewire\Form;
use Vigilant\Users\Actions\Fortify\PasswordValidationRules;

class UpdatePasswordForm extends Form
{
    use PasswordValidationRules;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ];
    }
}
