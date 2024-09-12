<?php

namespace Vigilant\Crawler\Enums;

enum Status: int
{
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case PAYMENT_REQUIRED = 402;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case NOT_ACCEPTABLE = 406;
    case PROXY_AUTHENTICATION_REQUIRED = 407;
    case REQUEST_TIMEOUT = 408;
    case CONFLICT = 409;
    case GONE = 410;
    case LENGTH_REQUIRED = 411;
    case PRECONDITION_FAILED = 412;
    case PAYLOAD_TOO_LARGE = 413;
    case URI_TOO_LONG = 414;
    case UNSUPPORTED_MEDIA_TYPE = 415;
    case RANGE_NOT_SATISFIABLE = 416;
    case EXPECTATION_FAILED = 417;
    case IM_A_TEAPOT = 418;
    case MISDIRECTED_REQUEST = 421;
    case UNPROCESSABLE_ENTITY = 422;
    case LOCKED = 423;
    case FAILED_DEPENDENCY = 424;
    case TOO_EARLY = 425;
    case UPGRADE_REQUIRED = 426;
    case PRECONDITION_REQUIRED = 428;
    case RATE_LIMITED = 429;
    case REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    case UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;
    case HTTP_VERSION_NOT_SUPPORTED = 505;
    case VARIANT_ALSO_NEGOTIATES = 506;
    case INSUFFICIENT_STORAGE = 507;
    case LOOP_DETECTED = 508;
    case NOT_EXTENDED = 510;
    case NETWORK_AUTHENTICATION_REQUIRED = 511;

    // Cloudflare-specific status codes
    case CLOUDFLARE_UNKNOWN_ERROR = 520;
    case CLOUDFLARE_WEB_SERVER_DOWN = 521;
    case CLOUDFLARE_CONNECTION_TIMED_OUT = 522;
    case CLOUDFLARE_ORIGIN_UNREACHABLE = 523;
    case CLOUDFLARE_TIMEOUT = 524;
    case CLOUDFLARE_SSL_HANDSHAKE_FAILED = 525;
    case CLOUDFLARE_INVALID_SSL_CERTIFICATE = 526;
    case CLOUDFLARE_RAILGUN_ERROR = 527;
    case CLOUDFLARE_ERROR = 530;

    public function label(): string
    {
        return match ($this) {
            Status::BAD_REQUEST => 'Bad Request',
            Status::UNAUTHORIZED => 'Unauthorized',
            Status::PAYMENT_REQUIRED => 'Payment Required',
            Status::FORBIDDEN => 'Forbidden',
            Status::NOT_FOUND => 'Not Found',
            Status::METHOD_NOT_ALLOWED => 'Method Not Allowed',
            Status::NOT_ACCEPTABLE => 'Not Acceptable',
            Status::PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
            Status::REQUEST_TIMEOUT => 'Request Timeout',
            Status::CONFLICT => 'Conflict',
            Status::GONE => 'Gone',
            Status::LENGTH_REQUIRED => 'Length Required',
            Status::PRECONDITION_FAILED => 'Precondition Failed',
            Status::PAYLOAD_TOO_LARGE => 'Payload Too Large',
            Status::URI_TOO_LONG => 'URI Too Long',
            Status::UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
            Status::RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable',
            Status::EXPECTATION_FAILED => 'Expectation Failed',
            Status::IM_A_TEAPOT => "I'm a Teapot",
            Status::MISDIRECTED_REQUEST => 'Misdirected Request',
            Status::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
            Status::LOCKED => 'Locked',
            Status::FAILED_DEPENDENCY => 'Failed Dependency',
            Status::TOO_EARLY => 'Too Early',
            Status::UPGRADE_REQUIRED => 'Upgrade Required',
            Status::PRECONDITION_REQUIRED => 'Precondition Required',
            Status::RATE_LIMITED => 'Rate Limited',
            Status::REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
            Status::UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',
            Status::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            Status::NOT_IMPLEMENTED => 'Not Implemented',
            Status::BAD_GATEWAY => 'Bad Gateway',
            Status::SERVICE_UNAVAILABLE => 'Service Unavailable',
            Status::GATEWAY_TIMEOUT => 'Gateway Timeout',
            Status::HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
            Status::VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
            Status::INSUFFICIENT_STORAGE => 'Insufficient Storage',
            Status::LOOP_DETECTED => 'Loop Detected',
            Status::NOT_EXTENDED => 'Not Extended',
            Status::NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required',

            // Cloudflare-specific status codes
            Status::CLOUDFLARE_UNKNOWN_ERROR => 'Unknown Error (Cloudflare)',
            Status::CLOUDFLARE_WEB_SERVER_DOWN => 'Web Server Is Down (Cloudflare)',
            Status::CLOUDFLARE_CONNECTION_TIMED_OUT => 'Connection Timed Out (Cloudflare)',
            Status::CLOUDFLARE_ORIGIN_UNREACHABLE => 'Origin Is Unreachable (Cloudflare)',
            Status::CLOUDFLARE_TIMEOUT => 'A Timeout Occurred (Cloudflare)',
            Status::CLOUDFLARE_SSL_HANDSHAKE_FAILED => 'SSL Handshake Failed (Cloudflare)',
            Status::CLOUDFLARE_INVALID_SSL_CERTIFICATE => 'Invalid SSL Certificate (Cloudflare)',
            Status::CLOUDFLARE_RAILGUN_ERROR => 'Railgun Error (Cloudflare)',
            Status::CLOUDFLARE_ERROR => 'Cloudflare Error',
        };
    }
}
