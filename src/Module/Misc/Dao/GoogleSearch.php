<?php

namespace Botlife\Module\Misc\Dao;

class GoogleSearch extends \Botlife\Entity\SearchEngine
{
    
    public $id = 'google';
    
    public function search($searchTerms, $results = 1)
    {
        return false;
    }
    
}