<?php

namespace Vigilant\Dns\RecordParsers;

abstract class RecordParser
{
    public string $field = '';

    public function parse(array $result): ?string
    {
        if (array_key_exists($this->field, $result)) {
            return $result[$this->field];
        }

        $values = collect($result)->pluck($this->field);

        return $values->isEmpty()
            ? null
            : $values->implode(',');
    }
}
