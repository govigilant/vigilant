<?php

namespace Vigilant\Frontend\Integrations\Table;

use Closure;
use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Columns\BaseColumn;

class LinkColumn extends BaseColumn
{
    protected string $view = 'frontend::integrations.table.link-column';

    public ?Closure $linkCallback = null;

    public ?Closure $textCallback = null;

    public bool $newTab = false;

    public function link(Closure $linkCallback): static
    {
        $this->linkCallback = $linkCallback;

        return $this;
    }

    public function text(Closure $textCallback): static
    {
        $this->textCallback = $textCallback;

        return $this;
    }

    public function openInNewTab(bool $newTab = true): static
    {
        $this->newTab = $newTab;

        return $this;
    }

    public function render(Model $model): mixed
    {
        $url = $this->linkCallback !== null ? ($this->linkCallback)($model) : $this->resolveValue($model);
        $text = $this->textCallback !== null ? ($this->textCallback)($model) : $url;

        return view($this->view, [
            'link' => $url,
            'text' => $text,
            'newTab' => $this->newTab,
        ]);
    }
}
