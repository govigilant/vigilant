<?php

namespace Vigilant\Dns\RecordParsers;

class CNAME extends RecordParser
{
    public string $field = 'target';
}
