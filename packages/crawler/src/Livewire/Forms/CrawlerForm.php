<?php

namespace Vigilant\Crawler\Livewire\Forms;

use Livewire\Attributes\Locked;
use Livewire\Form;
use Vigilant\Crawler\Validation\EqualDomainRule;
use Vigilant\Frontend\Validation\CronExpression;

class CrawlerForm extends Form
{
    #[Locked]
    public ?int $site_id;

    public string $schedule = '0 0 * * *';

    public string $start_url = '';

    public ?array $sitemaps = [];

    public ?array $settings = [
        'scheduleConfig' => [
            'type' => 'monthly',
            'hour' => '9',
            'weekDay' => 1,
            'monthDay' => 1,
        ],
    ];

    public function rules(): array
    {
        return [
            'schedule' => ['required', new CronExpression],
            'start_url' => ['required', 'max:255', 'url'],
            'sitemaps' => ['required_without:start_url', 'array', new EqualDomainRule],
            'sitemaps.*' => ['required', 'url'],
            'settings' => ['array'],
        ];
    }
}
