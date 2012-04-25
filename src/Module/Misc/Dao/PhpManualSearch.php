<?php

namespace Botlife\Module\Misc\Dao;

use PhpManual\Doc\LanguageRef;

use PhpManual\Doc\ClassRef;

use PhpManual\Doc\FunctionRef;
use PhpManual\Doc\SearchSuggestions;
use PhpManual\PhpManual;

class PhpmanualSearch extends \Botlife\Entity\SearchEngine
{
    
    public $id       = 'php-manual';
    public $aliases  = array('php');
    
    public function search($searchTerms, $results = 1)
    {
        
        $url  = 'http://php.net/' . urlencode($searchTerms);
        $data = \DataGetter::getData('file-content', $url);
        if (!$data) {
            return false;
        }
        $manual = new PhpManual;
        $data = $manual->fromData($data);
        
        $info = new \StdClass;
        $info->searchTerms = $searchTerms;
        $info->searchEngine = $this;
        $info->entries = array();
        
        if ($data instanceof SearchSuggestions) {
            return false;
        } elseif ($data instanceof FunctionRef) {
            $result = new \StdClass;
            $result->id     = $data->phpFunction;
            $result->title  = $data->phpFunction;
            $result->description = $data->functionDescription;
            $result->prototype = $data->prototype;
            $result->url    = $url;
            $result->categories = array('function');
            $info->entries[0] = $result;
        } elseif ($data instanceof ClassRef) {
            $result = new \StdClass;
            $result->id     = $data->name;
            $result->title  = $data->pageTitle;
            $result->description = $data->description;
            $result->prototype = $data->constructSyntax;
            $result->url    = $url;
            $result->categories = array('class');
            $info->entries[0] = $result;
        } elseif ($data instanceof LanguageRef) {
            $result = new \StdClass;
            $result->id     = $data->title;
            $result->title  = $data->title;
            $result->description = $data->description;
            $result->url    = $url;
            $result->categories = array('language');
            $info->entries[0] = $result;
        }
        
        $info->results = count($info->entries);
        if (!$info->results) {
            return false;
        }
        return $info;
    }
    
}