<?php

namespace enko\RelativeDateParser;


class RelativeDateParser {

    /** @var  RelativeDateType */
    private $datetype = null;

    private $now = null;

    function __construct ($string,\DateTime $now = null, $lang = 'en') {
        // initialize the translator

        DateTranslator::getInstance($lang);

        // set the now date
        if (is_null($now)) {
            $this->now = new \DateTime();
        } else {
            $this->now = $now;
        }

        // then try to determine which datetype we have here
        $success = preg_match(WeekdayPatternRecurrence::getRegex(),$string,$match);
        if ($success === 1) {
            $this->datetype = new WeekdayPatternRecurrence($match);
            return $this;
        }
        $success = preg_match(IntervalRecurrence::getRegex(),$string,$match);
        if ($success === 1) {
            $this->datetype = new IntervalRecurrence($match);
            return $this;
        }

        $success = preg_match(MonthDayRecurrence::getRegex(),$string,$match);
        if ($success === 1) {
            $this->datetype = new MonthDayRecurrence($match);
            return $this;
        }

        throw new \Exception('No DateType found.');

    }

    public function getCurrent() {
        return $this->datetype->getCurrent($this->now);
    }

    public function getNext() {
        return $this->datetype->getNext($this->now);
    }

    public function setNow(\DateTime $now) {
        $this->now = $now;
    }
}