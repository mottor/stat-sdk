<?php

namespace Mottor\Stat\Domain\Visit\Model;

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
    private $createdAt;

    /**
     * @var boolean
     */
    private $isUnique;

    /**
     * Visit constructor.
     *
     * @param integer           $siteId
     * @param DateTimeInterface $createdAt
     * @param boolean           $isUnique [optional]
     *
     * @throws Exception
     */
    public function __construct($siteId, DateTimeInterface $createdAt, $isUnique = false) {
        if (!preg_match('/^[1-9]\d*$/', $siteId)) {
            throw new Exception("Argument 'siteId' must be a positive integer");
        }

        $this->siteId = (int) $siteId;
        $this->createdAt = clone $createdAt;
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
    public function getCreatedAt() {
        return $this->createdAt;
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
     * @return static
     * @throws Exception
     */
    public static function createFromArray(array $properties) {
        if (!isset($properties['siteId'])) {
            throw new InvalidArgumentException('siteId is not set');
        }

        if (!isset($properties['createdAt'])) {
            throw new InvalidArgumentException('createdAt is not set');
        }

        if (!isset($properties['isUnique'])) {
            throw new InvalidArgumentException('isUnique is not set');
        }

        $createdAt = new DateTimeImmutable($properties['createdAt']);

        return new static(
            $properties['siteId'],
            $createdAt,
            $properties['isUnique']
        );
    }

    /**
     * @return array
     */
    public function toArray() {
        $createdAt = $this
            ->getCreatedAt()
            ->format(self::DATE_FORMAT);

        return [
            'siteId'    => $this->getSiteId(),
            'createdAt' => $createdAt,
            'isUnique'  => $this->isUnique()
        ];
    }
}