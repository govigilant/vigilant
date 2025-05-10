<?php

namespace Vigilant\Uptime\Http\Livewire\Forms;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Vigilant\Core\Validation\CanEnableRule;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Models\Monitor;

class CreateUptimeMonitorForm extends Form
{
    #[Locked]
    public ?int $site_id;

    #[Validate('required|max:255')]
    public string $name = '';

    public bool $enabled = true;

    public string $type = Type::Http->value;

    public array $settings = [
        'host' => '',
    ];

    public int $interval = 60;

    #[Validate('required|integer|min:0|max:3')]
    public ?int $retries = 0;

    #[Validate('required|integer|max:10')]
    public ?int $timeout = 5;

    public function getRules(): array
    {
        return array_merge(parent::getRules(),
            [
                'type' => ['required', Rule::enum(Type::class)],
                'name' => ['required', 'string', 'max:255'],
                'interval' => ['required', 'integer', 'in:'.implode(',', array_keys(config('uptime.intervals')))],
                'settings.port' => ['integer', 'min:0', 'max:65535', 'required_if:type,ping'],
                'settings.host' => ['required_if:type,ping,http'],
                'enabled' => ['boolean', new CanEnableRule(Monitor::class)],
            ]);
    }
}
