<?php

namespace Vigilant\Lighthouse\Data;

use Vigilant\Core\Data\Data;

class CategoryResultDifferenceData extends Data
{
    public array $rules = [
        'performance_old' => ['required', 'numeric'],
        'performance_new' => ['required', 'numeric'],

        'accessibility_old' => ['required', 'numeric'],
        'accessibility_new' => ['required', 'numeric'],

        'best_practices_old' => ['required', 'numeric'],
        'best_practices_new' => ['required', 'numeric'],

        'seo_old' => ['required', 'numeric'],
        'seo_new' => ['required', 'numeric'],
    ];

    public function performanceOld(): float
    {
        return $this['performance_old'];
    }

    public function performanceNew(): float
    {
        return $this['performance_new'];
    }

    public function performanceDifference(): float
    {
        return $this->calculateDifference($this->performanceOld(), $this->performanceNew());
    }

    public function accessibilityOld(): float
    {
        return $this['accessibility_old'];
    }

    public function accessibilityNew(): float
    {
        return $this['accessibility_new'];
    }

    public function accessibilityDifference(): float
    {
        return $this->calculateDifference($this->accessibilityOld(), $this->accessibilityNew());
    }

    public function bestPracticesOld(): float
    {
        return $this['best_practices_old'];
    }

    public function bestPracticesNew(): float
    {
        return $this['best_practices_new'];
    }

    public function bestPracticesDifference(): float
    {
        return $this->calculateDifference($this->bestPracticesOld(), $this->bestPracticesNew());
    }

    public function seoOld(): float
    {
        return $this['seo_old'];
    }

    public function seoNew(): float
    {
        return $this['seo_new'];
    }

    public function seoDifference(): float
    {
        return $this->calculateDifference($this->seoOld(), $this->seoNew());
    }

    public function averageDifference(): float
    {
        $differences = [
            $this->performanceDifference(),
            $this->accessibilityDifference(),
            $this->bestPracticesDifference(),
            $this->seoDifference(),
        ];

        return array_sum($differences) / count($differences);
    }

    protected function calculateDifference(float $old, float $new): float
    {
        $difference = (($new - $old) / $old) * 100;

        return round($difference, 1);
    }
}
