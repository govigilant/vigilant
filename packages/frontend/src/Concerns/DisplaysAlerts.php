<?php

namespace Vigilant\Frontend\Concerns;

use Vigilant\Frontend\Enums\AlertType;

trait DisplaysAlerts
{
    protected function alert(string $title, string $message = '', AlertType $type = AlertType::Info): void
    {
       session()->flash('alert');
       session()->flash('alert-title', $title);
       session()->flash('alert-message', $message);
       session()->flash('alert-type', $type);
    }

    protected function alertNow(string $title, string $message = '', AlertType $type = AlertType::Info): void
    {
        session()->now('alert', true);
        session()->now('alert-title', $title);
        session()->now('alert-message', $message);
        session()->now('alert-type', $type);
    }
}
