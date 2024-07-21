<?php

namespace Vigilant\Dns\Enums;

use Vigilant\Dns\RecordParsers\RecordParser;

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
            self::A => DNS_A,
            self::AAAA => DNS_AAAA,
            self::CNAME => DNS_CNAME,
            self::MX => DNS_MX,
            self::NS => DNS_NS,
            self::PTR => DNS_PTR,
            self::SOA => DNS_SOA,
            self::SRV => DNS_SRV,
            self::TXT => DNS_TXT,
            self::NAPTR => DNS_NAPTR,
            self::CAA => DNS_CAA,
        };
    }

    public function parser(): RecordParser
    {
        $class = '\Vigilant\Dns\RecordParsers\\'.$this->name;

        throw_if(! class_exists($class), 'No parser for type '.$this->name);

        /** @var RecordParser $instance */
        $instance = app($class);

        return $instance;
    }
}
