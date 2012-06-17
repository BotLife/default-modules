<?php

namespace Botlife\Module\GoogleApps\Command;

use Botlife\Entity\SearchEngineHandler;

class YouTube extends \Botlife\Command\ACommand
{
    
    public $code   = 'youtube';
    public $action = 'init';
    public $regex  = '/^[.!@](youtube|yt)( )?(?P<title>.+)?$/i';
    
    public function init(\Ircbot\Type\MessageCommand $event)
    {
        $this->detectResponseType($event->message, $event->target);
        if (!isset($event->matches['title'])) {
            $this->respondWithPrefix(
                'You might want to specifiy a search term. '
                	. 'Example: !youtube ready 2 go'
            );
            return false;
        }
        $results = SearchEngineHandler::searchWithEngine(
        	'youtube', $event->matches['title']
    	);
        if (!$results) {
            $this->respondWithPrefix(
                'No YouTube video\'s where found.'
            );
            return false;
        }
        $this->run($results->entries[0]);
    }
    
    public function run($data)
    {
        $C = new \BotLife\Application\Colors;
        
        $this->respondWithInformation(array(
            'Title'     => $data->title . $C(101, '[')
                . $C(102, gmdate('H:i:s', $data->duration)) . $C(101, ']'),
            'Rating'    => array(
                \DataGetter::getData('star-rating', $data->rating->average / 20),
                array(
                    'Likes'    => number_format($data->rating->likes),
                    'Dislikes' => number_format($data->rating->dislikes),
                ),
            ),
            'Uploaded'  => $data->date->format('Y-m-d'),
            'URL'       => $data->url,
        ),  $C(101, 'You') . $C(102, 'Tube'));
    }
    
}
