<?php

namespace Vigilant\Dns\RecordParsers;

class SOA extends RecordParser
{
    public function parse(array $result): ?string
    {
        return $result['mname'];
    }
}
