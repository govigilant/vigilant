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
            Level::Info => 0x3498db,    // Blue
            Level::Warning => 0xf1c40f, // Yellow
            Level::Critical => 0xe74c3c,   // Red
            Level::Success => 0x2ecc71, // Green
        };
    }
}
