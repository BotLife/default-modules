<?php

namespace Botlife\Entity;

abstract class SearchEngine
{
    
    public $priority = 5;
    public $id       = null;
    public $aliases  = array();
    public $filters  = array();
    
    public function __construct()
    {
        \DataGetter::addCallback(
        	'search-engine', $this->id, array($this, 'search'),
        	$this->priority
    	);
        SearchEngineHandler::addSearchEngine($this, $this->aliases);
    }
    
    abstract public function search(
        $searchTerms, $results = 1, $filters = array()
    );
    
}