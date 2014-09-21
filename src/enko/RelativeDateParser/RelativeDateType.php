<?php

namespace enko\RelativeDateParser;

abstract class RelativeDateType {
    abstract public function getNext(\DateTime $now);
    abstract public function getCurrent(\DateTime $now);
}