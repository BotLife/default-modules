<?php

namespace Botlife\Module\Misc\Dao;

class YoutubeSearch extends \Botlife\Entity\SearchEngine
{
    
    public $id       = 'youtube';
    public $priority = 15;
    public $aliases  = array('yt');
    
    public function search($searchTerms, $results = 1)
    {
        $url        = 'https://gdata.youtube.com/feeds/api/videos?';
        $parameters = array(
            'q'           => $searchTerms,
            'alt'         => 'json',
            'max-results' => $results,
            'v'           => 2,
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
        foreach ($data->feed->entry as $entry) {
            $result = new \StdClass;
            $mediaGroup = $entry->{'media$group'};
            $result->id    = $mediaGroup->{'yt$videoid'}->{'$t'};
            $result->title = $entry->title->{'$t'};
            $result->date  = new \DateTime(
                $mediaGroup->{'yt$uploaded'}->{'$t'}
            );
            $result->votes  = (int) $entry->{'gd$rating'}->numRaters;
            $result->rating = new \StdClass;
            $result->rating->average = (int) $entry->{'gd$rating'}->average * 20;
            $result->rating->likes = (int) $entry->{'yt$rating'}->numLikes;
            $result->rating->dislikes = (int) $entry->{'yt$rating'}->numDislikes;
            $result->duration = (int) $mediaGroup->{'yt$duration'}->{'seconds'}; 
            $result->description = $mediaGroup->{'media$description'}->{'$t'};
            $result->url   = 'http://youtu.be/' . $result->id;
            $info->entries[] = $result;
        }
        $info->results = count($info->entries);
        if (!$info->results) {
            return false;
        }
        return $info;
    }
    
}