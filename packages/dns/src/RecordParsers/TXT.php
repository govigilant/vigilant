<?php

namespace Vigilant\Dns\RecordParsers;

class TXT extends RecordParser
{
    public function parse(array $result): ?string
    {
        return $result['txt'];
    }
}
