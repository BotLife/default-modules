<?php

namespace Botlife\Module;

class Misc extends AModule
{

    public $commands = array(
        '\Botlife\Module\Misc\Command\Translate',
        '\Botlife\Module\Misc\Command\EightBall',
        '\Botlife\Module\Misc\Command\NineGag',
    	'\Botlife\Module\Misc\Command\Imdb',
    	'\Botlife\Module\Misc\Command\UrlShortener',
    );

    public function __construct()
    {
        $starRating = new Misc\Dao\StarRating;
        \DataGetter::addCallback(
        	'star-rating', 'star-rating-color',
        	array($starRating, 'getRating'), 50
        );
        $translator = new Misc\Dao\Translator;
        \DataGetter::addCallback(
        	'translator', 'google-translate',
            array($translator, 'translateWithGoogle'), 55
        );
        \DataGetter::addCallback(
        	'translator', 'mymemory-translate',
            array($translator, 'translateWithMyMemory'), 50
        );
        \DataGetter::addCallback(
        	'translator', 'leet-translate',
            array($translator, 'translateWithLeet'), 60
        );
        $youtubeShortener = new Misc\Dao\YoutubeShortener;
        \DataGetter::addCallback(
        	'url-shortener', 'youtube',
            array($youtubeShortener, 'shorten'), 70
        );
        $imdbSearch = new Misc\Dao\ImdbSearch;
        $phpSearch = new Misc\Dao\PhpManualSearch;
        parent::__construct();
    }
    
}
