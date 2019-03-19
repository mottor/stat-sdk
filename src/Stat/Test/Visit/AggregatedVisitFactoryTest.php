<?php

namespace Mottor\Stat\Test\Visit;

use Mottor\Stat\Domain\Visit\Factory\AggregatedVisitFactory;
use Mottor\Stat\Domain\Visit\Model\AggregatedVisit;
use Mottor\Stat\Domain\Visit\Model\Visit;
use PHPUnit\Framework\TestCase;

class AggregatedVisitFactoryTest extends TestCase
{
    public function testThatFactoryCreationWorks() {
        $visits = [
            [
                'siteId'    => 1024,
                'createdAt' => '2019-01-01',
                'isUnique'  => true
            ],
            [
                'siteId'    => 2048,
                'createdAt' => '2019-01-01',
                'isUnique'  => false
            ],
            [
                'siteId'    => 1024,
                'createdAt' => '2019-01-01',
                'isUnique'  => false
            ],
            [
                'siteId'    => 1024,
                'createdAt' => '2019-02-02',
                'isUnique'  => true
            ],
        ];

        $visits = array_map(
            function (array $visit) {
                return Visit::createFromArray($visit);
            },
            $visits
        );

        $aggregatedVisits = AggregatedVisitFactory::createFromVisits($visits);

        $this->assertCount(3, $aggregatedVisits);
        $this->assertContainsOnlyInstancesOf(AggregatedVisit::class, $aggregatedVisits);

        $aggregatedVisit = $aggregatedVisits[0];

        $this->assertSame(1024, $aggregatedVisit->getSiteId());
        $this->assertSame('2019-01-01', $aggregatedVisit->getAggregatedAt()->format(AggregatedVisit::DATE_FORMAT));
        $this->assertSame(2, $aggregatedVisit->getCount());
        $this->assertSame(1, $aggregatedVisit->getUniqueCount());

        $aggregatedVisit = $aggregatedVisits[1];

        $this->assertSame(2048, $aggregatedVisit->getSiteId());
        $this->assertSame('2019-01-01', $aggregatedVisit->getAggregatedAt()->format(AggregatedVisit::DATE_FORMAT));
        $this->assertSame(1, $aggregatedVisit->getCount());
        $this->assertSame(0, $aggregatedVisit->getUniqueCount());

        $aggregatedVisit = $aggregatedVisits[2];

        $this->assertSame(1024, $aggregatedVisit->getSiteId());
        $this->assertSame('2019-02-02', $aggregatedVisit->getAggregatedAt()->format(AggregatedVisit::DATE_FORMAT));
        $this->assertSame(1, $aggregatedVisit->getCount());
        $this->assertSame(1, $aggregatedVisit->getUniqueCount());
    }
}