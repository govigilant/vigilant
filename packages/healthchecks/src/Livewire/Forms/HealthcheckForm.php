<?php

namespace Vigilant\Healthchecks\Livewire\Forms;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;
use Vigilant\Core\Validation\CanEnableRule;
use Vigilant\Healthchecks\Enums\Type;
use Vigilant\Healthchecks\Models\Healthcheck;

class HealthcheckForm extends Form
{
    #[Locked]
    public ?int $site_id;

    public bool $enabled = true;

    public string $domain = '';

    public Type $type = Type::Endpoint;

    public ?string $endpoint = null;

    public int $interval = 60;

    public function rules(): array
    {
        return [
            'domain' => ['required', 'string', 'max:255', 'url'],
            'type' => ['required', Rule::enum(Type::class)],
            'endpoint' => ['nullable', 'string', 'max:255', 'required_if:type,endpoint'],
            'interval' => ['required', 'integer', 'in:'.implode(',', array_keys(config('healthchecks.intervals')))],
            'enabled' => ['boolean', new CanEnableRule(Healthcheck::class)],
        ];
    }

    public function cleanDomain(): void
    {
        if (empty($this->domain)) {
            return;
        }

        $parsed = parse_url($this->domain);
        if ($parsed === false) {
            return;
        }

        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? $this->domain;
        $port = isset($parsed['port']) ? ":{$parsed['port']}" : '';

        $this->domain = "{$scheme}://{$host}{$port}";
    }
}
