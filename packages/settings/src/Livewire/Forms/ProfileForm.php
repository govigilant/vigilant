<?php

namespace Vigilant\Settings\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ProfileForm extends Form
{
    #[Validate('required|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';
}
