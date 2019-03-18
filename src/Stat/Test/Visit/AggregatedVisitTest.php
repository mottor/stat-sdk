<?php

namespace Mottor\Stat\Test\Visit;

use DateTime;
use Mottor\Stat\Domain\Visit\Model\AggregatedVisit;

class AggregatedVisitTest extends \PHPUnit\Framework\TestCase
{
    public function testAggregatedVisitProperties() {
        $aggregatedAt = new DateTime();

        $aggregatedVisit = new AggregatedVisit(512, $aggregatedAt, 64, 128);

        foreach (['siteId', 'aggregatedAt', 'count', 'uniqueCount'] as $property) {
            $this->assertObjectHasAttribute($property, $aggregatedVisit);
        }

        $this->assertEquals(512, $aggregatedVisit->getSiteId());
        $this->assertEquals($aggregatedAt, $aggregatedVisit->getAggregatedAt());
        $this->assertEquals(64, $aggregatedVisit->getCount());
        $this->assertEquals(128, $aggregatedVisit->getUniqueCount());
    }

    public function testIncrementCount() {
        $aggregatedAt = new DateTime();

        $aggregatedVisit = new AggregatedVisit(1024, $aggregatedAt, 128, 256);
        $incrementedVisit = $aggregatedVisit->incrementCount();

        $this->assertEquals(128, $aggregatedVisit->getCount());

        $this->assertEquals(1024, $incrementedVisit->getSiteId());
        $this->assertEquals($aggregatedAt, $incrementedVisit->getAggregatedAt());
        $this->assertEquals(129, $incrementedVisit->getCount());
        $this->assertEquals(256, $incrementedVisit->getUniqueCount());
    }

    public function testIncrementUniqueCount() {
        $aggregatedAt = new DateTime();

        $aggregatedVisit = new AggregatedVisit(2056, $aggregatedAt, 256, 512);
        $incrementedVisit = $aggregatedVisit->incrementUniqueCount();

        $this->assertEquals(512, $aggregatedVisit->getUniqueCount());

        $this->assertEquals(2056, $incrementedVisit->getSiteId());
        $this->assertEquals($aggregatedAt, $incrementedVisit->getAggregatedAt());
        $this->assertEquals(256, $incrementedVisit->getCount());
        $this->assertEquals(513, $incrementedVisit->getUniqueCount());
    }
}