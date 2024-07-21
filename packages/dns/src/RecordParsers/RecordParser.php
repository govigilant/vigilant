<?php

namespace Vigilant\Dns\RecordParsers;

abstract class RecordParser
{
    abstract public function parse(array $result): ?string;
}
