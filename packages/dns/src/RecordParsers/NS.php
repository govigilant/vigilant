<?php

namespace Vigilant\Dns\RecordParsers;

class NS extends RecordParser
{
    public function parse(array $result): ?string
    {
        return $result['target'];
    }
}
