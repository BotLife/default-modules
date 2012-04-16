<?php

namespace Botlife\Module\Misc\Dao;

class Translator
{
    
    const LANG_ENGLISH = 1;
    const LANG_DUTCH   = 2;
    const LANG_RUSSIAN = 3;
    const LANG_LEET    = 4;
    
    const TRANSLATOR_DEFAULT  = 1;
    const TRANSLATOR_MYMEMORY = 2;
    const TRANSLATOR_LEET = 3;
    
    static private $_mapping = array(
        'en'      => self::LANG_ENGLISH,
    	'english' => self::LANG_ENGLISH,
        'nl'      => self::LANG_DUTCH,
    	'dutch'   => self::LANG_DUTCH,
    	'ru'      => self::LANG_RUSSIAN,
    	'russian' => self::LANG_RUSSIAN,
    	'1337'    => self::LANG_LEET,
    	'leet'    => self::LANG_LEET,
    	'le'      => self::LANG_LEET,
    );
    
    private $_translatorMapping = array(
        self::TRANSLATOR_DEFAULT => array(
            self::LANG_ENGLISH => 'en',
            self::LANG_DUTCH   => 'nl',
            self::LANG_RUSSIAN => 'ru',
        ),
    );
    
    private $_supported = array(
        self::TRANSLATOR_MYMEMORY => array(
            self::LANG_ENGLISH,
            self::LANG_DUTCH,
            self::LANG_RUSSIAN,
        ),
        self::TRANSLATOR_LEET => array(
            self::LANG_ENGLISH,
            self::LANG_DUTCH,
        ),
    );
    
    static public $naming = array(
        self::LANG_ENGLISH => 'English',
        self::LANG_DUTCH   => 'Dutch',
        self::LANG_RUSSIAN => 'Russian',
        self::LANG_LEET    => 'Leetspeak',
    );
    
    public function translateWithMyMemory($from, $to, $text)
    {
        if (!$this->translatorSupports(self::TRANSLATOR_MYMEMORY, $from)
            || !$this->translatorSupports(self::TRANSLATOR_MYMEMORY, $to)
        ) {
            return false;
        }
        $fromCode = $this->languageCode(self::TRANSLATOR_MYMEMORY, $from);
        $toCode = $this->languageCode(self::TRANSLATOR_MYMEMORY, $to);
        $url = 'http://mymemory.translated.net/api/get';
        $url .= '?q=' . urlencode($text);
        $url .= '&langpair=' . $fromCode . '|' . $toCode;
        $data = json_decode(\DataGetter::getData('file-content', $url));
        if (!$data->responseData->translatedText) {
            return false;
        }
        $info = new \StdClass;
        $info->from = $from;
        $info->to   = $to;
        $info->text = $data->responseData->translatedText;
        return $info;
    }
    
    public function translateWithLeet($from, $to, $text)
    {
        if (!$this->translatorSupports(self::TRANSLATOR_LEET, $from)
            || $to != self::LANG_LEET
        ) {
            return false;
        }
        $letters = array(
        	'a' => 4,
        	'b' => 8,
            'e' => 3,
        	'g' => 6,
            'l' => 1,
        	'o' => 0,
            's' => 5,
            't' => 7,
            'z' => 2,
        );
        $text = str_replace(array_keys($letters), array_values($letters), $text);
        $info = new \StdClass;
        $info->from = $from;
        $info->to   = $to;
        $info->text = $text;
        return $info;
    }
    
    public function translatorSupports($translator, $language)
    {
        return in_array($language, $this->_supported[$translator]);
    }
    
    static public function languageId($name)
    {
        $name = strtolower($name);
        return (isset(self::$_mapping[$name]))
                ? self::$_mapping[$name]
                : false;
    }

    public function languageCode($translator, $language)
    {
        if (isset($this->_translatorMapping[$translator][$language])) {
            return $this->_translatorMapping[$translator][$language];
        }
        if (isset($this->_translatorMapping[self::TRANSLATOR_DEFAULT][$language])) {
            return $this->_translatorMapping[self::TRANSLATOR_DEFAULT][$language];
        }
        return false;
    }
    
    static public function languageName($lang)
    {
        return (isset(self::$naming[$lang])) ? self::$naming[$lang] : null;
    }
    
}