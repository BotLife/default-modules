<?php

namespace Botlife\Module\Misc\Command;

use Botlife\Module\Misc\Dao\SearchEngine;

class Imdb extends \Botlife\Command\ACommand
{
    
    public $code   = 'imdb';
    public $action = 'init';
    public $regex  = '/^[.!@]imdb( )?(?P<title>\w+)?$/i';
    
    public function init(\Ircbot\Type\MessageCommand $event)
    {
        $this->detectResponseType($event->message, $event->target);
        if (!isset($event->matches['title'])) {
            $this->respondWithPrefix(
                'You might want to specifiy a movie title. '
                	. 'Example: !imdb 2012'
            );
            return false;
        }
        $results = SearchEngine::searchWithEngine(
        	'imdb', $event->matches['title']
    	);
        if (!$results) {
            $this->respondWithPrefix(
                'Could not find information related to that movie.'
            );
            return false;
        }
        $movie = $results->entries[0];
        $this->run($movie);
    }
    
    public function run($data)
    {
        $c    = new \BotLife\Application\Colors;
        $this->respondWithInformation(array(
        	'Title' => $data->title . (($data->duration) ? $c(12, '[')
                . $c(3, gmdate('H:i:s', $data->duration)) . $c(12, ']') : null),
            'Rating' => array(
                \DataGetter::getData(
                	'star-rating', $data->rating->average / 20
            	),
                array(
                    'Likes'    => number_format($data->rating->likes),
                    'Dislikes' => number_format($data->rating->dislikes),
                ),
            ),
            'Released' => ($data->date) ? $data->date->format('Y-m-d')
                : 'Unknown',
            'Plot' => $data->description,
            'URL'  => $data->url,
        ));
    }
    
}