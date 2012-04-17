<?php

namespace Botlife\Module;

class Misc extends AModule
{

    public $commands = array(
        '\Botlife\Module\Misc\Command\Translate',
        '\Botlife\Module\Misc\Command\YouTube',
        '\Botlife\Module\Misc\Command\EightBall',
        '\Botlife\Module\Misc\Command\NineGag',
    	'\Botlife\Module\Misc\Command\Imdb',
    	'\Botlife\Module\Misc\Command\UrlShortener',
    	'\Botlife\Module\Misc\Command\Search',
    );

    public function __construct()
    {
        $imdb = new Misc\Dao\MovieInfo;
        \DataGetter::addCallback(
        	'movie-info', 'imdb-search', array($imdb, 'getVideoInfo'), 50
    	);
        $starRating = new Misc\Dao\StarRating;
        \DataGetter::addCallback(
        	'star-rating', 'star-rating-color',
        	array($starRating, 'getRating'), 50
        );
        $doCurl = new Misc\Dao\DoCurl;
        \DataGetter::addCallback(
        	'file-content', 'curl-content',
            array($doCurl, 'doCurl'), 25
        );
        \DataGetter::addCallback(
        	'file-content', 'file-get-content',
            array($doCurl, 'fileGetContents'), 50
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
        $urlShortener = new Misc\Dao\UrlShortener;
        \DataGetter::addCallback(
        	'url-shortener', 'google',
            array($urlShortener, 'usingGoogle'), 60
        );
        $googleSearch = new Misc\Dao\GoogleSearch;
        $youtubeSearch = new Misc\Dao\YoutubeSearch;
        parent::__construct();
    }
    
}
