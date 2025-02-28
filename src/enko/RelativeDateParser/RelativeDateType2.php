<?php

namespace enko\RelativeDateParser;

class RelativeDateType2 extends RelativeDateType {
    private $interval = -1;
    private $days_or_week = -1;

    public static function getRegex() {
        return DateTranslator::getInstance()->translate('/^Alle (\d+) ([Wochen|Tage]+)$/im');
    }

    public function getCurrent(\DateTime $now) {
        return $now;
    }

    public function getNext(\DateTime $now) {
        $date = clone $now;
        if ($this->days_or_week == 0) {
            $date->add(\DateInterval::createFromDateString(sprintf('+%s days',$this->interval)));
        } else {
            $date->add(\DateInterval::createFromDateString(sprintf('+%s weeks',$this->interval)));
        }
        return $date;
    }

    function __construct($data) {
        $interval = intval($data[1]);
        if (is_integer($interval)) {
            $this->interval = $data[1];
        } else {
            throw new \BadMethodCallException("interval is wrong or missing");
        }

        switch($data[2]) {
            case DateTranslator::getInstance()->translate('Tage'):
                $this->days_or_week = 0;
                break;
            case DateTranslator::getInstance()->translate('Wochen'):
                $this->days_or_week = 1;
                break;
            default:
                throw new \BadMethodCallException("days or weeks are missing");
        }
    }
}