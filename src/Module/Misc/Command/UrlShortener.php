<?php

namespace Botlife\Module\Misc\Command;

use Ircbot\Type\MessageCommand;

class UrlShortener extends \Botlife\Command\ACommand
{

    public $regex = array(
        '/^[.!@]shorten?( (?P<url>.*))?$/i',
    );
    public $action = 'run';
    public $code   = 'url-shortener';
    
    public function run(MessageCommand $event)
    {
        $this->detectResponseType($event->message, $event->target);
        if (!isset($event->matches['url'])) {
            $this->respondWithPrefix('You need to specify a URL');
            return;
        }
        $shortenedUrl = \DataGetter::getData('url-shortener',
            $event->matches['url']
        );
        if (!$shortenedUrl) {
            $this->respondWithPrefix($c(12, 'Could not shorten your URL.'));
            return;
        }
        $this->respondWithInformation(array(
            'Long URL'        => $event->matches['url'],
            'Shortened URL'   => $shortenedUrl
        ));
    }
    
}
