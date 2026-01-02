<?php

namespace Vigilant\Notifications\Http\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateChannelForm extends Form
{
    #[Validate('required|max:255')]
    public string $channel = '';

    #[Validate('nullable|string|max:255')]
    public ?string $name = null;

    public array $settings = [];
}
