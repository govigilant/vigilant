<?php

namespace Vigilant\Dns\Enums;

use BlueLibraries\Dns\Records\RecordTypes;
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
            self::A => RecordTypes::A,
            self::AAAA => RecordTypes::AAAA,
            self::CNAME => RecordTypes::CNAME,
            self::MX => RecordTypes::MX,
            self::NS => RecordTypes::NS,
            self::PTR => RecordTypes::PTR,
            self::SOA => RecordTypes::SOA,
            self::SRV => RecordTypes::SRV,
            self::TXT => RecordTypes::TXT,
            self::NAPTR => RecordTypes::NAPTR,
            self::CAA => RecordTypes::CAA,
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

    public static function geoIpableTypes(): array
    {
        return [
            Type::A,
            Type::AAAA,
            Type::CNAME,
            Type::MX,
            Type::PTR,
        ];
    }
}
