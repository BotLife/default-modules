<?php

namespace Botlife\Module\GoogleApps\Dao\SearchEngine;

class Shopping extends \Botlife\Entity\SearchEngine
{
    
    public $id       = 'shopping';
    public $aliases  = array('shop');
    
    public function search($searchTerms, $results = 1, $filters = array())
    {
        $url        = 'https://www.googleapis.com/shopping/search/v1/public'
        	. '/products?';
        $parameters = array(
            'key'         => 'AIzaSyCAzLZhuRY3G006I7RgKBjW0xVhILOVvmA',
            'q'           => $searchTerms,
            'alt'         => 'json',
            'maxResults'  => $results,
            'country'     => 'us',
            'fields'      => 'items(id,product/title,product/creationTime,'
            	. 'product/description,product/inventories,product/link)',
        );
        $url .= http_build_query($parameters);
        $data = \DataGetter::getData('file-content',
            $url
        );
        if (!$data) {
            return false;
        }
        $data = json_decode($data);
        $info = new \StdClass;
        $info->searchTerms = $searchTerms;
        $info->searchEngine = $this;
        $info->entries = array();
        foreach ($data->items as $entry) {
            $result = new \StdClass;
            $result->id    = $entry->id;
            $result->title = $entry->product->title;
            $result->date  = new \DateTime($entry->product->creationTime);
            $result->description = $entry->product->description;
            if (isset($entry->product->inventories[0])) {
                $result->price = new \StdClass;
                $result->price->amount = $entry->product->inventories[0]->price; 
                $result->price->currency = $entry->product->inventories[0]
                    ->currency;
            }
            $result->url   = $entry->product->link;
            $info->entries[] = $result;
        }
        $info->results = count($info->entries);
        if (!$info->results) {
            return false;
        }
        return $info;
    }
    
}
