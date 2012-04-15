<?php

namespace Botlife\Module\Misc\Command;

class Imdb extends \Botlife\Command\ACommand
{
    
    public $code   = 'imdb';
    public $action = 'init';
    public $regex  = '/[.!@]imdb( )?(?P<title>\w+)?/i';
    
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
        $movie = \DataGetter::getData('movie-info', $event->matches['title']);
        if (!$movie) {
            $this->respondWithPrefix(
                'Could not find information related to that movie.'
            );
            return false;
        }
        $this->run($movie);
    }
    
    public function run($data)
    {
        $c    = new \BotLife\Application\Colors;
        $this->respondWithInformation(array(
        	'Title' => $data->title . (($data->duration) ? $c(12, '[')
                . $c(3, gmdate('H:i:s', $data->duration)) . $c(12, ']') : null),
            'Rating' => array(
                \DataGetter::getData('star-rating', $data->ratingAverage),
                array(
                    'Likes'    => number_format($data->ratingLikes),
                    'Dislikes' => number_format($data->ratingDislikes),
                ),
            ),
            'Released' => ($data->released) ? $data->released->format('Y-m-d')
                : 'Unknown',
            'Plot' => $data->plot,
            'URL'  => $data->url,
        ));
    }
    
}