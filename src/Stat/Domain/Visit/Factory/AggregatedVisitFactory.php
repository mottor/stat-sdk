<?php

namespace Mottor\Stat\Domain\Visit\Factory;

use Exception;
use Mottor\Stat\Domain\Visit\Model\AggregatedVisit;
use Mottor\Stat\Domain\Visit\Model\Visit;

class AggregatedVisitFactory
{
    /**
     * @param Visit[] $visits
     *
     * @return AggregatedVisit[]
     * @throws Exception
     */
    public static function createFromVisits(array $visits) {
        $map = [];

        foreach ($visits as $visit) {
            $siteId = $visit->getSiteId();

            $createdAt = $visit->getCreatedAt();
            $formattedCreatedAt = $createdAt->format(Visit::DATE_FORMAT);

            if (isset($map[$formattedCreatedAt][$siteId])) {
                $aggregatedVisit = $map[$formattedCreatedAt][$siteId];
            } else {
                $aggregatedVisit = new AggregatedVisit($siteId, $createdAt);
            }

            $aggregatedVisit = $aggregatedVisit->incrementCount();

            if ($visit->isUnique()) {
                $aggregatedVisit = $aggregatedVisit->incrementUniqueCount();
            }

            $map[$formattedCreatedAt][$siteId] = $aggregatedVisit;
        }

        $aggregatedVisits = [];

        foreach ($map as $i) {
            foreach ($i as $j) {
                $aggregatedVisits[] = $j;
            }
        }
        return $aggregatedVisits;
    }
}