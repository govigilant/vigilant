<?php

namespace Vigilant\Dns\Actions;

use Vigilant\Dns\Enums\Type;

class ResolveRecord
{
    public function resolve(Type $type, string $record): ?string
    {
        $result = dns_get_record($record, $type->flag());

        $parser = $type->parser();

        return $parser->parse($result);
    }
}
