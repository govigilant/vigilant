<?php

namespace Vigilant\Lighthouse\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Frontend\Traits\CanBeInline;
use Vigilant\Lighthouse\Models\LighthouseSite;

class LighthouseSiteForm extends Component
{
    use DisplaysAlerts;
    use CanBeInline;

    public Forms\LighthouseSiteForm $form;

    #[Locked]
    public LighthouseSite $lighthouseSite;

    public function mount(?LighthouseSite $site): void
    {
        if ($site !== null) {
            $this->form->fill($site->toArray());
            $this->lighthouseSite = $site;
        }
    }

    #[On('save')]
    public function save(): void
    {
        $this->validate();

        if ($this->lighthouseSite->exists) {
            $this->lighthouseSite->update($this->form->all());
        } else {
            $this->lighthouseSite = LighthouseSite::query()->create(
                $this->form->all()
            );
        }

        if (! $this->inline) {
            $this->alert(
                __('Saved'),
                __('Lighthouse monitor was successfully :action', ['action' => $this->lighthouseSite->wasRecentlyCreated ? 'created' : 'saved']),
                AlertType::Success
            );
            $this->redirectRoute('lighthouse');
        }
    }


    public function render(): mixed
    {
        return view('lighthouse::livewire.lighthouse-site-form', [
            'updating' => $this->lighthouseSite->exists,
        ]);
    }
}
