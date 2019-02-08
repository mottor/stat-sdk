<?php

namespace Mottor\Stat\Domain\Visit\ValueObject;

use DateTimeInterface;
use Exception;

class Visit
{
    /**
     * @var integer
     */
    private $siteId;

    /**
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
}