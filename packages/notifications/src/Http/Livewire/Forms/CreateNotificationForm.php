<?php

namespace Vigilant\Notifications\Http\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateNotificationForm extends Form
{
    #[Validate('required|max:255')]
    public string $name = '';

    #[Validate('required|max:255')]
    public string $notification = '';

    #[Validate('array')]
    public array $conditions = [];

    public bool $all_channels = false;
}
