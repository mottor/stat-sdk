<?php

namespace Mottor\Stat\Test\Visit;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
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

    public function testVisitCreateFromArrayMethod() {
        $visit = [
            'siteId'   => 1,
            'date'     => '2019-01-01',
            'isUnique' => false
        ];

        $visit = Visit::createFromArray($visit);

        $this->assertEquals(1, $visit->getSiteId());
        $this->assertEquals(false, $visit->isUnique());

        $this->assertInstanceOf(DateTimeInterface::class, $visit->getDate());

        $dateAsString = $visit
            ->getDate()
            ->format(Visit::DATE_FORMAT);

        $this->assertEquals('2019-01-01', $dateAsString);
    }

    public function testCreateFromArrayWithoutSiteId() {
        $visit = [
            'date'     => '2015-01-02',
            'isUnique' => true
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('siteId is not set');

        Visit::createFromArray($visit);
    }

    public function testCreateFromArrayWithoutDate() {
        $visit = [
            'siteId'   => 4,
            'isUnique' => false
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('date is not set');

        Visit::createFromArray($visit);
    }

    public function testCreateFromArrayWithoutIsUnique() {
        $visit = [
            'siteId' => 2,
            'date'   => '2016-05-07'
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('isUnique is not set');

        Visit::createFromArray($visit);
    }

    public function testVisitToArrayMethod() {
        $visit = new Visit(2, new DateTimeImmutable('2020-03-03'), true);

        $expected = [
            'siteId'   => 2,
            'date'     => '2020-03-03',
            'isUnique' => true
        ];

        $this->assertEquals($expected, $visit->toArray());
    }
}