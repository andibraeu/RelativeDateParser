<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 30.09.2014
 * Time: 21:47
 */

namespace enko\RelativeDateParser;

class RelativeDateType3 extends RelativeDateType {
    private $day = -1;

    public static function getRegex() {
        return DateTranslator::getInstance()->translate('/^Jeder (\d+)\. Tag eines Monats$/im');
    }

    private function getDay() {
        return $this->day;
    }

    public function getCurrent(\DateTime $now) {
        if ($this->day > cal_days_in_month(CAL_GREGORIAN,$now->format('m'),$now->format('Y'))) {
            throw new \BadMethodCallException(DateTranslator::getInstance()->translate('Angegebener Tag ist nicht im Monat %date% enthalten.',['date' => $now->format('F Y')]));
        }
        $date = new \DateTime();
        $date->setTimestamp (strtotime (sprintf ('%s %s %d', $this->getDay (), $now->format('F'), $now->format('Y'))));
        $date->setTime($now->format('H'),$now->format('i'));

        return $date;

    }

    public function getNext(\DateTime $now) {
        $date = clone $now;
        $date->add(\DateInterval::createFromDateString('+1 Month'));

        return $this->getCurrent($date);
    }

    function __construct($data) {
        $day = intval($data[1]);
        if (is_integer($day)) {
            $this->day = $day;
        } else {
            throw new \BadMethodCallException(DateTranslator::getInstance()->translate('Tag wurde nicht angegeben oder ist falsch.'));
        }
    }
} 