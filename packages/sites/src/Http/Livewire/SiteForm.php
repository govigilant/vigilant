<?php

namespace Vigilant\Sites\Http\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Sites\Http\Livewire\Forms\CreateSiteForm;
use Vigilant\Sites\Models\Site;

class SiteForm extends Component
{
    use DisplaysAlerts;

    public CreateSiteForm $form;

    #[Locked]
    public Site $site;

    public string $tab = 'uptime';

    public function mount(?Site $site): void
    {
        if ($site !== null) {
            $this->form->fill($site->toArray());
            $this->site = $site;
        }
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function save(): void
    {
        // Save tabs
        if ($this->site->exists) {
            $this->dispatch('save');
        }

        $this->validate();

        if ($this->site->exists) {
            $this->site->update($this->form->all());
        } else {
            $this->site = Site::query()->create(
                $this->form->all()
            );
        }

        $this->alert(
            __('Saved'),
            __('Site was successfully :action', ['action' => $this->site->wasRecentlyCreated ? 'created' : 'saved']),
            AlertType::Success
        );

        $this->redirectRoute('site.edit', ['site' => $this->site]);
    }

    public function render(): View
    {
        return view('sites::livewire.form', [
            'updating' => $this->site->exists,
            'tabs' => $this->tabs(),
        ]);
    }

    protected function tabs(): array
    {
        return [
            'uptime' => [
                'title' => __('Uptime Monitoring'),
                'component' => 'sites.tabs.uptime-monitor',
            ],
        ];
    }
}
