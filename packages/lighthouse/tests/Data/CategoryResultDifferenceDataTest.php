<?php

namespace Vigilant\Lighthouse\Tests\Data;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Data\CategoryResultDifferenceData;
use Vigilant\Lighthouse\Tests\TestCase;

class CategoryResultDifferenceDataTest extends TestCase
{
    #[Test]
    public function test_calculations(): void
    {
        $data = CategoryResultDifferenceData::of([
            'performance_old' => 0.5,
            'performance_new' => 1,
            'accessibility_old' => 1,
            'accessibility_new' => 0.5,
            'best_practices_old' => 1,
            'best_practices_new' => 1,
            'seo_old' => 0,
            'seo_new' => 0,
        ]);

        $this->assertEquals(100, $data->performanceDifference());
        $this->assertEquals(-50, $data->accessibilityDifference());
        $this->assertEquals(0, $data->bestPracticesDifference());
        $this->assertEquals(0, $data->seoDifference());
    }
}
