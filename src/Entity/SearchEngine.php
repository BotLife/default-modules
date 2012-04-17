<?php

namespace Botlife\Entity;

use Botlife\Module\Misc\Dao\SearchEngine as SEngine;

abstract class SearchEngine
{
    
    public $priority = 5;
    public $id       = null;
    public $aliases  = array();
    
    public function __construct()
    {
        \DataGetter::addCallback(
        	'search-engine', $this->id, array($this, 'search'),
        	$this->priority
    	);
        SEngine::addSearchEngine($this, $this->aliases);
    }
    
    abstract public function search($searchTerms, $results = 1);
    
}