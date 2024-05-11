<?php

namespace Vigilant\Lighthouse\Livewire\Forms;

use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LighthouseSiteForm extends Form
{
    #[Locked]
    public ?int $site_id;

    #[Validate('required|url|max:255')]
    public string $url = '';

    public array $settings = [
        'host' => '',
    ];

    #[Validate('required')]
    public string $interval = '0 */3 * * *';
}
