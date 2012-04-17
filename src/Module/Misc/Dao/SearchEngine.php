<?php

namespace Botlife\Module\Misc\Dao;

class SearchEngine
{
    
    static private $_mapping = array();
    static private $_engines = array();
    
    static public function addSearchEngine(
        \Botlife\Entity\SearchEngine &$engine, $aliases = array()
    ) {
        self::$_engines[$engine->id] = $engine;
        foreach ($aliases as $alias) {
            self::$_mapping[$alias] = $engine->id;
        }
    }
    
    static public function search($terms, $results = 1)
    {
        return \DataGetter::getData('search-engine', $terms, $results);
    }
    
    static public function searchWithEngine($id, $terms, $results = 1)
    {
        if (isset(self::$_mapping[$id])) {
            $id = self::$_mapping[$id];
        }
        if (!isset(self::$_engines[$id])) {
            throw new \Exception('Unknown searchengine "' . $id . '"');
        }
        return self::$_engines[$id]->search($terms, $results);
    }
    
    
}