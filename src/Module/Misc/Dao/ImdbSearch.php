<?php

namespace Botlife\Module\Misc\Dao;

class ImdbSearch extends \Botlife\Entity\SearchEngine
{
    
    public $id       = 'imdb';
    public $priority = 10;
    public $aliases  = array('movie');
    
    public function search($searchTerms, $results = 1)
    {
        
        $url        = 'http://www.imdbapi.com/?';
        $parameters = array(
            't' => $searchTerms,
            'i' => '',
        );
        $url .= http_build_query($parameters);
        $data = \DataGetter::getData('file-content',
            $url
        );
        if (!$data) {
            return false;
        }
        $data = json_decode($data);
        if (!isset($data->Title)) {
            return false;
        }
        $info = new \StdClass;
        $info->searchTerms = $searchTerms;
        $info->searchEngine = $this;
        $info->entries = array();
        
        $result = new \StdClass;
        $result->id     = $data->ID;
        $result->title  = $data->Title;
        $result->description = $data->Plot;
        $result->url    = 'http://www.imdb.com/title/' . $result->id . '/';
        if ($data->Released != 'N/A') {
            $result->date = new \DateTime($data->Released);
        } else {
            $result->date = null;
        }
        $result->duration = $this->_getDuration($data->Runtime);
        $result->rating = new \StdClass;
        $result->votes = $data->Votes;
        $result->rating->average = (float) $data->Rating * 10;
        list($result->rating->likes, $result->rating->dislikes) = $this
            ->_splitRating($data->Rating, $result->votes);
        $info->entries[0] = $result;
        
        $info->results = count($info->entries);
        if (!$info->results) {
            return false;
        }
        return $info;
    }
    
    private function _getDuration($text)
    {
        $pattern = '';
        $pattern .= '/^';
        $pattern .= '((?P<hours>\d+) hr(s)?( )?)?';
        $pattern .= '((?P<mins>\d+) min(s)?( )?)?';
        $pattern .= '$/';
        preg_match($pattern, $text, $matches);
        $duration = 0;
        if (isset($matches['hours'])) {
            $duration += 3600 * (int) $matches['hours'];
        }
        if (isset($matches['mins'])) {
            $duration += 60 * (int) $matches['mins'];
        }
        return $duration;
    }
    
    private function _splitRating($average, $amount)
    {
        $like = (float) $average * 10;
        $dislike = (100 - $like);
        $like *= $amount / 100;
        $dislike *= $amount / 100;
        return array($like, $dislike);
    }
    
}