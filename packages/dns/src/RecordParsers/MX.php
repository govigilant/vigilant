<?php

namespace Vigilant\Dns\RecordParsers;

class MX extends RecordParser
{
    public function parse(array $result): ?string
    {
        return $result['target'];
    }
}
