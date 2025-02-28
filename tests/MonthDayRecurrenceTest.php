<?php

use enko\RelativeDateParser\RelativeDateParser;
use PHPUnit\Framework\TestCase;

class MonthDayRecurrenceTest extends TestCase
{
    public function testNextDate()
    {
        $now = new DateTime('2014-07-07');
        $next_date = new DateTime('2014-08-05');

        /** @var RelativeDateParser $parser */
        $parser = new RelativeDateParser('Jeder 5. Tag eines Monats', $now, 'de');


        // Assert
        $this->assertEquals($next_date->format('c'), $parser->getNext()->format('c'));
    }
}