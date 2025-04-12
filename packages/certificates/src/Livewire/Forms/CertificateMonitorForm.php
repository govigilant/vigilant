<?php

namespace Vigilant\Certificates\Livewire\Forms;

use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Core\Validation\CanEnableRule;
use Vigilant\Frontend\Validation\Fqdn;

class CertificateMonitorForm extends Form
{
    #[Locked]
    public ?int $site_id;

    public string $domain = '';

    #[Validate('required|integer|min:1|max:65535')]
    public int $port = 443;

    public bool $enabled = true;

    public function getRules(): array
    {
        return array_merge(parent::getRules(),
            [
                'enabled' => ['boolean', new CanEnableRule(CertificateMonitor::class)],
                'domain' => ['required', 'string', 'max:255', new Fqdn],
            ]
        );
    }
}
