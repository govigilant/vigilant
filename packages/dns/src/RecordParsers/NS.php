<?php

namespace Vigilant\Dns\RecordParsers;

class NS extends RecordParser
{
    public string $field = 'target';
}
