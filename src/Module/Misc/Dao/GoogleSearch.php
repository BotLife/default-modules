<?php

namespace Botlife\Module\Misc\Dao;

class GoogleSearch extends \Botlife\Entity\SearchEngine
{
    
    public $id       = 'google';
    public $priority = 20;
    public $aliases  = array('g');
    
    public function search($searchTerms, $results = 1)
    {
        return false;
    }
    
}