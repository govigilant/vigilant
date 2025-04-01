<?php

namespace Vigilant\Dns\Actions;

use BlueLibraries\Dns\Records\AbstractRecord;
use Vigilant\Dns\Client\DnsClient;
use Vigilant\Dns\Enums\Type;

class ResolveRecord
{
    public function __construct(protected DnsClient $client) {}

    public function resolve(Type $type, string $record): ?string
    {
        $result = collect($this->client->get($record, $type->flag()))
            ->map(fn (AbstractRecord $record): array => $record->toArray())
            ->toArray();

        if (count($result) === 0) {
            logger()->debug('DNS Resolver: Record Not Found', [
                'type' => $type->name,
                'record' => $record,
            ]);

            return null;
        }

        if (count($result) === 1) {
            $result = $result[0];
        }

        $parser = $type->parser();

        return $parser->parse($result);
    }
}
