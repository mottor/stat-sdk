<?php

namespace Mottor\Stat\Domain\Visit\ValueObject;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;

class Visit
{
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @var integer
     */
    private $siteId;

    /**
     * Represents the date of the visit without specifying the time
     *
     * @var DateTimeInterface
     */
    private $date;

    /**
     * @var boolean
     */
    private $isUnique;

    /**
     * Visit constructor.
     *
     * @param integer           $siteId
     * @param DateTimeInterface $date
     * @param boolean           $isUnique [optional]
     *
     * @throws Exception
     */
    public function __construct($siteId, DateTimeInterface $date, $isUnique = false) {
        if (!preg_match('/^[1-9]\d*$/', $siteId)) {
            throw new Exception("Argument 'siteId' must be a positive integer");
        }

        $this->siteId = (int) $siteId;
        $this->date = $date;
        $this->isUnique = (bool) $isUnique;
    }

    /**
     * @return integer
     */
    public function getSiteId() {
        return $this->siteId;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * @return boolean
     */
    public function isUnique() {
        return $this->isUnique;
    }

    /**
     * @param array $properties
     *
     * @return Visit
     * @throws Exception
     */
    public static function createFromArray(array $properties) {
        if (!isset($properties['siteId'])) {
            throw new InvalidArgumentException('siteId is not set');
        }

        if (!isset($properties['date'])) {
            throw new InvalidArgumentException('date is not set');
        }

        if (!isset($properties['isUnique'])) {
            throw new InvalidArgumentException('isUnique is not set');
        }

        $date = new DateTimeImmutable($properties['date']);

        return new static(
            $properties['siteId'],
            $date,
            $properties['isUnique']
        );
    }

    /**
     * @return array
     */
    public function toArray() {
        $date = $this
            ->getDate()
            ->format(self::DATE_FORMAT);

        return [
            'siteId'   => $this->getSiteId(),
            'date'     => $date,
            'isUnique' => $this->isUnique()
        ];
    }
}