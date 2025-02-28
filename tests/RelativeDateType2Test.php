<?php

use enko\RelativeDateParser\RelativeDateParser;
use PHPUnit\Framework\TestCase;

class RelativeDateType2Test extends TestCase
{
    public function testNextDate()
    {
        $now = new DateTime('2014-07-07');
        $next_date = new DateTime('2014-07-21');

        /** @var RelativeDateParser $parser */
        $parser = new RelativeDateParser('Alle 14 Tage', $now, 'de');


        // Assert
        $this->assertEquals($next_date->format('c'), $parser->getNext()->format('c'));
    }
}