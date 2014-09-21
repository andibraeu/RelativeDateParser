<?php

namespace enko\RelativeDateParser;


class RelativeDateType1 {
    private $ordinal = -1;
    private $day = -1;
    private $month_or_year = -1;

    public static function getRegex() {
        return DateTranslator::getInstance()->translate('/([Erster|Zweiter|Dritter|Letzter]+) ([Montag|Dienstag|Mittwoch|Donnerstag|Freitag|Samstag|Sonntag]+) des ([Monats|Jahres]+)/im');
    }

    private function getOrdinal() {
        switch($this->ordinal) {
            case 0:
                return 'first';
            case 1:
                return 'second';
            case 2:
                return 'third';
            case 3:
                return 'last';
            default:
                return null;
        }
    }

    private function getDay() {
        switch($this->day) {
            case 0:
                return 'mon';
            case 1;
                return 'tue';
            case 2:
                return 'wed';
            case 3:
                return 'thu';
            case 4:
                return 'fri';
            case 5:
                return 'sat';
            case 6:
                return 'sun';
            default:
                return null;
        }
    }

    public function getCurrent(\DateTime $now) {
        $date = new \DateTime();
        $date->setTimestamp (strtotime (sprintf ('%s %s %s %d', $this->getOrdinal (), $this->getDay (), $now->format('F'), $now->format('Y'))));
        $date->setTime($now->format('H'),$now->format('i'));

        return $date;

    }

    public function getNext(\DateTime $now) {
        $date = clone $now;
        $date->add(\DateInterval::createFromDateString('+1 Month'));

        return $this->getCurrent($date);
    }

    function __construct($data) {
        switch($data[1]) {
            case DateTranslator::getInstance()->translate('Erster'):
                $this->ordinal = 0;
                break;
            case DateTranslator::getInstance()->translate('Zweiter'):
                $this->ordinal = 1;
                break;
            case DateTranslator::getInstance()->translate('Dritter'):
                $this->ordinal = 2;
                break;
            case DateTranslator::getInstance()->translate('Letzter'):
                $this->ordinal = 3;
                break;
            default:
                throw new \BadMethodCallException("Ordinal is wrong or missing");
        }

        switch ($data[2]) {
            case DateTranslator::getInstance()->translate('Montag'):
                $this->day = 0;
                break;
            case DateTranslator::getInstance()->translate('Dienstag'):
                $this->day = 1;
                break;
            case DateTranslator::getInstance()->translate('Mittwoch'):
                $this->day = 2;
                break;
            case DateTranslator::getInstance()->translate('Donnerstag'):
                $this->day = 3;
                break;
            case DateTranslator::getInstance()->translate('Freitag'):
                $this->day = 4;
                break;
            case DateTranslator::getInstance()->translate('Samstag'):
                $this->day = 5;
                break;
            case DateTranslator::getInstance()->translate('Sonntag'):
                $this->day = 6;
                break;
            default:
                throw new \BadMethodCallException("Day is wrong or missing");
        }

        switch($data[3]) {
            case DateTranslator::getInstance()->translate('Monats'):
                $this->month_or_year = 0;
                break;
            case DateTranslator::getInstance()->translate('Jahres'):
                $this->month_or_year = 1;
                break;
            default:
                throw new \BadMethodCallException("month or year is wrong or missing");
        }
    }
}