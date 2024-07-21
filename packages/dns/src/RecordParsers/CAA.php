<?php

namespace Vigilant\Dns\RecordParsers;

class CAA extends RecordParser
{
    public function parse(array $result): ?string
    {
        return $result['value'];
    }
}
