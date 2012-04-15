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
        parent::__construct();
    }
    
}
