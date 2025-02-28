<?php

namespace enko\RelativeDateParser;

use Symfony\Component\Translation\Translator;
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
            throw new \BadMethodCallException('Language is missing');
        }

        $translations_path = realpath(__DIR__ . '/../../../translations');
        $dir_iterator = new \RecursiveDirectoryIterator($translations_path);
        $iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::SELF_FIRST);

        $this->translator = new Translator($lang);
        $this->translator->addLoader('pofile', new PoFileLoader());
        
        $translationFileFound = false;
        
        foreach ($iterator as $file) {
            if (basename($file,'.po') == $lang) {
                $this->translator->addResource('pofile', realpath($file), $lang);
                $translationFileFound = true;
            }
        }

        if (!$translationFileFound) {
            throw new \BadMethodCallException('No translation file for this language available.');
        }
    }

    public function translate($string, $params = []) {
        return $this->translator->trans($string, $params);
    }
}