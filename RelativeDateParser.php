<?php

require "vendor/autoload.php";

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\PoFileLoader;

class DateTranslator {
  /** @var DateTranslator */
  private static $instance = null;

  private $translator = null;

  public static function getInstance($lang = '') {
    if (is_null(static::$instance)) {
      static::$instance = new static($lang);
    }

    return static::$instance;
  }

  private function __construct($lang) {
    if ($lang == '') {
      throw new BadMethodCallException('Language is missing');
    }
    $dir_iterator = new RecursiveDirectoryIterator("translations");
    $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

    foreach ($iterator as $file) {
      if (basename($file,'.po') == $lang) {
        $loader = new PoFileLoader();
        $loader->load(realpath($file),$lang);

        $this->translator = new Translator($lang);
        $this->translator->addLoader('pofile', $loader);
        $this->translator->addResource('pofile',realpath($file),$lang);
      }
    }

    if (is_null($this->translator)) {
      throw new BadMethodCallException('No translation file for this language available.');
    }
  }

  public function translate($string) {
    return $this->translator->trans($string);
  }
}


abstract class RelativeDateType {
  abstract public function getNext(DateTime $now);
  abstract public function getCurrent(DateTime $now);
}

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

  public function getCurrent(DateTime $now) {
    $date = new DateTime();
    $date->setTimestamp (strtotime (sprintf ('%s %s %s %d', $this->getOrdinal (), $this->getDay (), $now->format('F'), $now->format('Y'))));
    $date->setTime($now->format('H'),$now->format('i'));

    return $date;

  }

  public function getNext(DateTime $now) {
    $date = clone $now;
    $date->add(DateInterval::createFromDateString('+1 Month'));

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
        throw new BadMethodCallException("Ordinal is wrong or missing");
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
        throw new BadMethodCallException("Day is wrong or missing");
    }

    switch($data[3]) {
      case DateTranslator::getInstance()->translate('Monats'):
        $this->month_or_year = 0;
        break;
      case DateTranslator::getInstance()->translate('Jahres'):
        $this->month_or_year = 1;
        break;
      default:
        throw new BadMethodCallException("month or year is wrong or missing");
    }
  }
}

class RelativeDateType2 {
  private $interval = -1;
  private $days_or_week = -1;

  public static function getRegex() {
    return DateTranslator::getInstance()->translate('/Alle (\d+) ([Wochen|Tage]+)/im');
  }

  public function getCurrent(DateTime $now) {
    return $now;
  }

  public function getNext(DateTime $now) {
    $date = clone $now;
    if ($this->days_or_week == 0) {
      $date->add(DateInterval::createFromDateString(sprintf('+%s days',$this->interval)));
    } else {
      $date->add(DateInterval::createFromDateString(sprintf('+%s weeks',$this->interval)));
    }
    return $date;
  }

  function __construct($data) {
    $interval = intval($data[1]);
    if (is_integer($interval)) {
      $this->interval = $data[1];
    } else {
      throw new BadMethodCallException("interval is wrong or missing");
    }

    switch($data[2]) {
      case DateTranslator::getInstance()->translate('Tage'):
        $this->days_or_week = 0;
        break;
      case DateTranslator::getInstance()->translate('Wochen'):
        $this->days_or_week = 1;
        break;
      default:
        throw new BadMethodCallException("days or weeks are missing");
    }
  }
}

class RelativeDateParser {

  /** @var  RelativeDateType */
  private $datetype = null;

  private $now = null;

  function __construct ($string,DateTime $now = null, $lang = 'en') {
    // initialize the translator

    DateTranslator::getInstance($lang);

    // set the now date
    if (is_null($now)) {
      $this->now = new DateTime();
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

  public function setNow(DateTime $now) {
    $this->now = $now;
  }
} 