<?php

namespace Vigilant\Sites\Http\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateSiteForm extends Form
{
    #[Validate('required|url')]
    public string $url = '';
}
