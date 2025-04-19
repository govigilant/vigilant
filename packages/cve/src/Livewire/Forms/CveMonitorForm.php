<?php

namespace Vigilant\Cve\Livewire\Forms;

use Livewire\Attributes\Locked;
use Livewire\Form;
use Vigilant\Core\Validation\CanEnableRule;
use Vigilant\Dns\Models\DnsMonitor;

class CveMonitorForm extends Form
{
    #[Locked]
    public ?int $site_id;

    public bool $enabled = true;

    public string $keyword = '';

    public function rules(): array
    {
        return [
            'keyword' => ['required', 'max:255'],
            'enabled' => ['boolean', new CanEnableRule(DnsMonitor::class)],
        ];
    }
}
