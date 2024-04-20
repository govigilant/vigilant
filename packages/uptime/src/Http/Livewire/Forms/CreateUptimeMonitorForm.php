<?php

namespace Vigilant\Uptime\Http\Livewire\Forms;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Vigilant\Uptime\Enums\Type;

class CreateUptimeMonitorForm extends Form
{
    #[Locked]
    public ?int $site_id;

    #[Validate('required|max:255')]
    public string $name = '';

    public string $type = Type::Http->value;

    public array $settings = [
        'host' => '',
    ];

    #[Validate('required')]
    public string $interval = '* * * * *';

    #[Validate('required|integer')]
    public int $retries = 1;

    #[Validate('required|integer')]
    public int $timeout = 5;

    public function getRules(): array
    {
        return array_merge(parent::getRules(),
            [
                'type' => ['required', Rule::enum(Type::class)],
                'settings.port' => ['integer', 'min:0', 'max:65535', 'required_if:type,ping'],
                'settings.host' => ['required_if:type,ping,http'],
            ]);
    }
}
