<?php

namespace Vigilant\Crawler\Enums;

enum State: string
{
    case Pending = 'pending';
    case Crawling = 'crawling';
    case Finished = 'finished';

    case Ratelimited = 'ratelimited';

    public function label(): string
    {
        return match ($this) {
            State::Pending => 'Pending',
            State::Crawling => 'Crawling',
            State::Finished => 'Finished',

            State::Ratelimited => 'Rate Limited',
        };
    }
}
