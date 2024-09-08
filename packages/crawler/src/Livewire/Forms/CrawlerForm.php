<?php

namespace Vigilant\Crawler\Livewire\Forms;

use Livewire\Attributes\Locked;
use Livewire\Form;
use Vigilant\Crawler\Validation\EqualDomainRule;

class CrawlerForm extends Form
{
    #[Locked]
    public ?int $site_id;

    public string $start_url = '';

    public ?array $sitemaps = [];

    public function rules(): array
    {
        return [
            'start_url' => ['required', 'max:255', 'url'],
            'sitemaps' => ['required_without:start_url', 'array', new EqualDomainRule],
            'sitemaps.*' => ['required', 'url'],
        ];
    }
}
