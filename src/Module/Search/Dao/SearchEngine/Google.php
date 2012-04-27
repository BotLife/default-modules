<?php

namespace Botlife\Module\Search\Dao\SearchEngine;

class Google extends \Botlife\Entity\SearchEngine
{
    
    public $id       = 'google';
    public $priority = 20;
    public $aliases  = array('g');
    
    public function search($searchTerms, $results = 1, $filters = array())
    {
        return false;
    }
    
}