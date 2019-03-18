<?php

namespace Mottor\Stat\Domain\Visit\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;

class AggregatedVisit
{
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @var integer
     */
    private $siteId;

    /**
     * Rrepresents the date when visits are aggregated without specifying the time
     *
     * @var DateTimeInterface
     */
    private $aggregatedAt;

    /**
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $uniqueCount;

    /**
     * Visit constructor.
     *
     * @param integer           $siteId
     * @param DateTimeInterface $aggregatedAt
     * @param int               $count
     * @param int               $uniqueCount
     *
     * @throws Exception
     */
    public function __construct($siteId, DateTimeInterface $aggregatedAt, $count, $uniqueCount) {
        if (!preg_match('/^[1-9]\d*$/', $siteId)) {
            throw new Exception("Argument 'siteId' must be a positive integer");
        }

        $this->siteId = (int) $siteId;
        $this->aggregatedAt = clone $aggregatedAt;
        $this->count = (int) $count;
        $this->uniqueCount = (int) $uniqueCount;
    }

    /**
     * @return int
     */
    public function getSiteId() {
        return $this->siteId;
    }

    /**
     * @return DateTimeInterface
     */
    public function getAggregatedAt() {
        return $this->aggregatedAt;
    }

    /**
     * @return int
     */
    public function getCount() {
        return $this->count;
    }

    /**
     * @return int
     */
    public function getUniqueCount() {
        return $this->uniqueCount;
    }

    /**
     * @param array $properties
     *
     * @return static
     * @throws Exception
     */
    public static function createFromArray(array $properties) {
        if (!isset($properties['siteId'])) {
            throw new InvalidArgumentException('siteId is not set');
        }

        if (!isset($properties['aggregatedAt'])) {
            throw new InvalidArgumentException('aggregatedAt is not set');
        }

        if (!isset($properties['count'])) {
            throw new InvalidArgumentException('count is not set');
        }

        if (!isset($properties['uniqueCount'])) {
            throw new InvalidArgumentException('uniqueCount is not set');
        }

        $aggregatedAt = new DateTimeImmutable($properties['aggregatedAt']);

        return new static(
            $properties['siteId'],
            $aggregatedAt,
            $properties['count'],
            $properties['uniqueCount']
        );
    }

    /**
     * @return array
     */
    public function toArray() {
        $aggregatedAt = $this
            ->getAggregatedAt()
            ->format(self::DATE_FORMAT);

        return [
            'siteId'       => $this->getSiteId(),
            'aggregatedAt' => $aggregatedAt,
            'count'        => $this->getCount(),
            'uniqueCount'  => $this->getUniqueCount()
        ];
    }

    /**
     * @return AggregatedVisit
     * @throws Exception
     */
    public function incrementCount() {
        $count = $this->getCount();
        $count++;

        return new static(
            $this->getSiteId(),
            $this->getAggregatedAt(),
            $count,
            $this->getUniqueCount()
        );
    }

    /**
     * @return AggregatedVisit
     * @throws Exception
     */
    public function incrementUniqueCount() {
        $uniqueCount = $this->getUniqueCount();
        $uniqueCount++;

        return new static(
            $this->getSiteId(),
            $this->getAggregatedAt(),
            $this->getCount(),
            $uniqueCount
        );
    }
}