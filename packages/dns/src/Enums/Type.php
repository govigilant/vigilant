<?php

namespace Vigilant\Dns\Enums;

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
    case SPF = 'SPF';
    case NAPTR = 'NAPTR';
    case DS = 'DS';
    case DNSKEY = 'DNSKEY';
    case RRSIG = 'RRSIG';
    case NSEC = 'NSEC';
    case DNAME = 'DNAME';
    case TLSA = 'TLSA';
    case CAA = 'CAA';
}
