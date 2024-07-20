<?php

namespace Vigilant\Dns\Enums;

use Vigilant\Dns\RecordParsers\A;
use Vigilant\Dns\RecordParsers\RecordParser;
use Vigilant\Dns\Records\BaseRecord;

enum Type: string
{
    case A = 'A';
    case AAAA = 'AAAA';
    case CNAME = 'CNAME';
    case MX = 'MX';
    case NS = 'NS';
    case PTR = 'PTR';
    case SOA = 'SOA';
    case SRV = 'SRV';
    case TXT = 'TXT';
    case NAPTR = 'NAPTR';
    case CAA = 'CAA';

    public function flag(): int
    {
        return match ($this) {
            static::A => DNS_A,
            static::AAAA => DNS_AAAA,
            static::CNAME => DNS_CNAME,
            static::MX => DNS_MX,
            static::NS => DNS_NS,
            static::PTR => DNS_PTR,
            static::SOA => DNS_SOA,
            static::SRV => DNS_SRV,
            static::TXT => DNS_TXT,
            static::NAPTR => DNS_NAPTR,
            static::CAA => DNS_CAA,
        };
    }

    public function parser(): RecordParser
    {
        $class = '\Vigilant\Dns\RecordParsers\\' . $this->name;

        throw_if(!class_exists($class), 'No parser for type ' . $this->name);

        /** @var RecordParser $instance */
        $instance = app($class);

        return $instance;
    }
}
