<?php

namespace Vigilant\Dns\RecordParsers;

class MX extends RecordParser
{
    public string $field = 'target';
}
