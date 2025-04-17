<?php

namespace Vigilant\Frontend\Concerns;

use Vigilant\Frontend\Enums\AlertType;

// @phpstan-ignore-next-line
trait DisplaysAlerts
{
    protected function alert(string $title, string $message = '', AlertType $type = AlertType::Info): void
    {
        session()->flash('alert');
        session()->flash('alert-title', $title);
        session()->flash('alert-message', $message);
        session()->flash('alert-type', $type);
    }

    protected function alertBrowser(string $title, string $message = '', AlertType $type = AlertType::Info): void
    {
        $this->dispatch('alert', [
            'id' => uniqid(),
            'title' => $title,
            'message' => $message,
            'type' => $type->value,
        ]);
    }
}
