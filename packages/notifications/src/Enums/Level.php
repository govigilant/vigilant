<?php

namespace Vigilant\Notifications\Enums;

enum Level: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Critical = 'critical';

    public function color(): int
    {
        return match ($this) {
            Level::Info => 0x3498DB,    // Blue
            Level::Warning => 0xF1C40F, // Yellow
            Level::Critical => 0xE74C3C,   // Red
            Level::Success => 0x2ECC71, // Green
        };
    }
}
