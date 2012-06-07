<?php

namespace Botlife\Module;

class GoogleApps extends AModule
{

    public function __construct()
    {
        $googleSearch = new GoogleApps\Dao\SearchEngine\Google;
        $youtubeSearch = new GoogleApps\Dao\SearchEngine\Youtube;
        $shopSearch = new GoogleApps\Dao\SearchEngine\Shopping;
        parent::__construct();
    }
    
}
