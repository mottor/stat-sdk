<?php

namespace Mottor\Stat\Test\Visit;

use DateTime;
use Exception;
use Mottor\Stat\Domain\Visit\ValueObject\Visit;

class VisitTest extends \PHPUnit\Framework\TestCase
{
    public function testVisitProperties() {
        $dateOfThisMoment = new DateTime();

        $visit = new Visit(1024, $dateOfThisMoment, true);

        foreach (['siteId', 'date', 'isUnique'] as $property) {
            $this->assertObjectHasAttribute($property, $visit);
        }

        $this->assertEquals(1024, $visit->getSiteId());
        $this->assertEquals($dateOfThisMoment, $visit->getDate());
        $this->assertEquals(true, $visit->isUnique());
    }

    public function testVisitSiteIdConstraintWithCorrectValues() {
        $dateOfThisMoment = new DateTime();

        $values = [1024, '2048'];

        foreach ($values as $value) {
            $visit = new Visit($value, $dateOfThisMoment);
            $this->assertEquals($value, $visit->getSiteId());
        }
    }

    public function testVisitSiteIdConstraintWithIncorrectValues() {
        $dateOfThisMoment = new DateTime();
        $incorrectValues = [-1024, 0, '-2048', '0', 'X1024', '2048X'];

        foreach ($incorrectValues as $value) {
            try {
                $visit = new Visit($value, $dateOfThisMoment);
                $this->fail(sprintf('Expected exception not thrown (value %s)', $visit->getSiteId()));
            } catch (Exception $exception) {
                $this->assertEquals("Argument 'siteId' must be a positive integer", $exception->getMessage());
            }
        }
    }
}