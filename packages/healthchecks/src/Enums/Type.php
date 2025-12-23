<?php

namespace Vigilant\Healthchecks\Enums;

use Vigilant\Healthchecks\Checks\Checker;
use Vigilant\Healthchecks\Checks\Endpoint;
use Vigilant\Healthchecks\Checks\Module;

enum Type: string
{
    case Endpoint = 'endpoint';
    case Laravel = 'laravel';
    case Statamic = 'statamic';
    case Magento = 'magento';
    case Wordpress = 'wordpress';
    case Joomla = 'joomla';
    case Drupal = 'drupal';

    public function label(): string
    {
        return match ($this) {
            default => ucfirst($this->value),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Endpoint => 'phosphor-heartbeat',
            self::Laravel => 'si-laravel',
            self::Statamic => 'si-statamic',
            self::Magento => 'bxl-magento',
            self::Wordpress => 'si-wordpress',
            self::Joomla => 'si-joomla',
            self::Drupal => 'si-drupal',
        };
    }

    public function endpoint(): ?string
    {
        return match ($this) {
            self::Endpoint => null,
            self::Magento => 'rest/V1/vigilant/health',
            self::Wordpress => 'wp-json/vigilant/v1/health',
            self::Joomla => 'index.php?option=com_vigilant&task=health.check',
            self::Drupal => 'vigilant/health',
            default => 'api/vigilant/health'
        };
    }

    public function checker(): Checker
    {
        $class = match ($this) {
            self::Endpoint => Endpoint::class,
            default => Module::class,
        };

        return app($class);
    }

    public function generatesOwnToken(): bool
    {
        return match ($this) {
            self::Magento => true,
            default => false,
        };
    }

    public function checksResponseKey(): string
    {
        return match ($this) {
            self::Magento => '0',
            default => 'checks',
        };
    }

    public function metricsResponseKey(): string
    {
        return match ($this) {
            self::Magento => '1',
            default => 'metrics',
        };
    }
}
