<?php

namespace Vigilant\Dns\RecordParsers;

class A extends RecordParser
{
    public function parse(array $result): ?string
    {
        $result = !isset($result['ip'])
         ? collect($result)->pluck('ip')->implode(',')
            : $result['ip'];

        return blank($result) ? null : $result;
    }
}
