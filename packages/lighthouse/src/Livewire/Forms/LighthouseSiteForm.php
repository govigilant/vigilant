<?php

namespace Vigilant\Lighthouse\Livewire\Forms;

use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Vigilant\Core\Validation\CanEnableRule;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class LighthouseSiteForm extends Form
{
    #[Locked]
    public ?int $site_id;

    #[Validate('required|url|max:255')]
    public string $url = '';

    public bool $enabled = true;

    public array $settings = [
        'host' => '',
    ];

    #[Validate('required')]
    public int $interval = 60 * 24;

    public function getRules(): array
    {
        return array_merge(parent::getRules(),
            [
                'enabled' => ['boolean', new CanEnableRule(LighthouseMonitor::class)],
            ]
        );
    }
}
