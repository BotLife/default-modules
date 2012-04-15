<?php

namespace Botlife\Module\Misc\Command;

use Ircbot\Type\MessageCommand;

class YouTube extends \Botlife\Command\ACommand
{

    public $regex = array(
        '/(http\:\/\/)?(www\.)?(youtube\.com\/watch\?(.*)?v\=(?P<idlong>[A-Za-z0-9_-]+)(\&(.*))?|youtu\.be\/(?P<id>[A-Za-z0-9_-]+))/',
    );
    public $action = 'lookup';
    public $code   = 'youtube';
    
    public $responseType    = self::RESPONSE_PUBLIC;
    
    private $_lastrun;
    private $_cache = array();
    
    public function lookup(MessageCommand $event)
    {
        if ((time() - $this->_lastrun) <= 3) {
            return;
        }
        $this->detectResponseType($event->message, $event->target);
        $this->_lastrun = time();
        $videoId = (empty($event->matches['id'])) ? $event->matches['idlong'] : $event->matches['id'];
        $data = $this->getData($videoId);
        if (!$data) {
            return false;
        }
        $C = new \BotLife\Application\Colors;
        
        $this->respondWithInformation(array(
            'Title'     => $data->title . $C(12, '[')
                . $C(3, gmdate('H:i:s', $data->duration)) . $C(12, ']'),
            'Rating'    => array(
                \DataGetter::getData('star-rating', $data->ratingAverage),
                array(
                    'Likes'    => number_format($data->ratingLikes),
                    'Dislikes' => number_format($data->ratingDislikes),
                ),
            ),
            'Uploaded'  => array(
                $data->uploaded->format('Y-m-d'),
                array(
                    $data->uploader,
                ),
            ),
            'Favorites' => number_format($data->timesFavorited),
            'Views'     => number_format($data->views)/*,
            'Cached'    => ($data->cached) ? 'Yes' : 'No'*/
        ),  $C(12, 'You') . $C(03, 'Tube'));
        
        foreach ($this->_cache as $key => $value)
            if ($value['time'] > (time() + 300))
                unset($this->_cache[$key]);
    }
    
    public function getData($videoId)
    {
        if (isset($this->_cache[$videoId])) {
            $this->_cache[$videoId]['object']->cached = true;
            return $this->_cache[$videoId]['object'];
        }
        $this->_cache[$videoId]['time'] = time();
        $dOM = new \DOMDocument();
        @$dOM->load('https://gdata.youtube.com/feeds/api/videos/' . $videoId . '?v=2');
        $video = new \StdClass;
        $video->title = $dOM->getElementsByTagName('title')->item(0)->nodeValue;
        $video->uploader = $dOM->getElementsByTagName('author')->item(0)
            ->getElementsByTagName('name')->item(0)->nodeValue;
        $group = $dOM->getElementsByTagName('group')->item(0);
        $video->uploaded = new \DateTime($dOM->getElementsByTagName('uploaded')->item(0)
            ->nodeValue);
        $video->duration = (int) $dOM->getElementsByTagName('duration')->item(0)
            ->getAttribute('seconds');
        $rating = $dOM->getElementsByTagName('rating')->item(0);
        if (!$rating) {
            $this->_cache[$videoId]['object'] = false;
            return false;
        }
        $video->ratingAverage = (float) $rating->getAttribute('average');
        $video->ratingTotal = (int) $rating->getAttribute('numRaters');
        $rating = $dOM->getElementsByTagName('rating')->item(1);
        $video->ratingLikes = (int) $rating->getAttribute('numLikes');
        $video->ratingDislikes = (int) $rating->getAttribute('numDislikes');
        $statistics = $dOM->getElementsByTagName('statistics')->item(0);
        $video->views = (int) $statistics->getAttribute('viewCount');
        $video->timesFavorited = (int) $statistics->getAttribute('favoriteCount');
        $video->cached = false;
        $this->_cache[$videoId]['object'] = $video;
        return $video;
    }
    
}
