<?php

namespace Vigilant\Dns\RecordParsers;

class AAAA extends RecordParser
{
    public function parse(array $result): ?string
    {
        $result = ! isset($result['ipv6'])
         ? collect($result)->pluck('ipv6')->implode(',')
            : $result['ipv6'];

        return blank($result) ? null : $result;
    }
}
