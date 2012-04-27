<?php

namespace Botlife\Entity;

class SearchEngineHandler
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
    
    static public function search($terms, $results = 1, $filters = array())
    {
        return \DataGetter::getData(
        	'search-engine', $terms, $results, $filters
        );
    }
    
    static public function searchWithEngine(
        $id, $terms, $results = 1, $filters = array()
    ) {
        if (isset(self::$_mapping[$id])) {
            $id = self::$_mapping[$id];
        }
        if (!isset(self::$_engines[$id])) {
            throw new \Exception('Unknown searchengine "' . $id . '"');
        }
        $engine = self::$_engines[$id];
        foreach ($filters as $filter) {
            if (!in_array($filter, $engine->filters)) {
                throw new \Exception('Unknown filter "' . $filter . '"');
            }
        }
        return $engine->search($terms, $results, $filters);
    }
    
    
}