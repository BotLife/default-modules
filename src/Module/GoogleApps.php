<?php

namespace Botlife\Module;

class GoogleApps extends AModule
{

    public $commands = array(
	    '\Botlife\Module\GoogleApps\Command\GoogleDocsInfo',
    );

    public function __construct()
    {
        \Botlife\Application\Config::addOption('google.key', 'string');
        $urlShortener = new GoogleApps\Dao\UrlShortener;
        \DataGetter::addCallback(
        	'url-shortener', 'google',
            array($urlShortener, 'usingGoogle'), 60
        );
        $googleSearch = new GoogleApps\Dao\SearchEngine\Google;
        $youtubeSearch = new GoogleApps\Dao\SearchEngine\Youtube;
        $shopSearch = new GoogleApps\Dao\SearchEngine\Shopping;
        parent::__construct();
    }
    
}
