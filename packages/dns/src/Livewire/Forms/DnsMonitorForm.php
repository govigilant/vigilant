<?php

namespace Vigilant\Dns\Livewire\Forms;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;
use Vigilant\Core\Validation\CanEnableRule;
use Vigilant\Dns\Enums\Type;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Frontend\Validation\Fqdn;

class DnsMonitorForm extends Form
{
    #[Locked]
    public ?int $site_id;

    public bool $enabled = true;

    public Type $type = Type::A;

    public string $record = '';

    public string $value = '';

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::enum(Type::class),
            ],
            'record' => ['required', 'max:255', new Fqdn],
            'value' => ['required', 'max:255'],
            'enabled' => ['boolean', new CanEnableRule(DnsMonitor::class)],
        ];
    }
}
