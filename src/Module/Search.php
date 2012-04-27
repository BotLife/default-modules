<?php

namespace Botlife\Module;

class Search extends AModule
{

    public $commands = array(
    	'\Botlife\Module\Search\Command\Search',
    );

    public function __construct()
    {
        $googleSearch = new Search\Dao\SearchEngine\Google;
        $youtubeSearch = new Search\Dao\SearchEngine\Youtube;
        $shopSearch = new Search\Dao\SearchEngine\Shopping;
        parent::__construct();
    }
    
}
