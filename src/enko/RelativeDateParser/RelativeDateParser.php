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
        $success = preg_match(RelativeDateType1::getRegex(),$string,$match);
        if ($success === 1) {
            $this->datetype = new RelativeDateType1($match);
            return $this;
        }
        $success = preg_match(RelativeDateType2::getRegex(),$string,$match);
        if ($success === 1) {
            $this->datetype = new RelativeDateType2($match);
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