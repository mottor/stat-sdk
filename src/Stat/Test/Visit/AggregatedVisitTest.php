<?php

namespace Mottor\Stat\Test\Visit;

use DateTime;
use Mottor\Stat\Domain\Visit\ValueObject\AggregatedVisit;

class AggregatedVisitTest extends \PHPUnit\Framework\TestCase
{
    public function testAggregatedVisitProperties() {
        $dateOfThisMoment = new DateTime();

        $visit = new AggregatedVisit(1024, $dateOfThisMoment, 128, 256);

        foreach (['siteId', 'aggregatedAt', 'count', 'uniqueCount'] as $property) {
            $this->assertObjectHasAttribute($property, $visit);
        }

        $this->assertEquals(1024, $visit->getSiteId());
        $this->assertEquals($dateOfThisMoment, $visit->getAggregatedAt());
        $this->assertEquals(128, $visit->getCount());
        $this->assertEquals(256, $visit->getUniqueCount());
    }
}