<?php

namespace Botlife\Module\Search\Command;

use Botlife\Entity\SearchEngineHandler;
use Ircbot\Type\MessageCommand;

class Search extends \Botlife\Command\ACommand
{

    public $regex = array(
        '/^[.!@]search( (\@(?P<engine>[a-zA-Z]+) )?(\[(?P<filter>[a-zA-Z-]+)\] )?(?P<terms>.*))?$/i',
    );

    public $action = 'run';
    public $code   = 'search';
    
    public function run(MessageCommand $event)
    {
        $this->detectResponseType($event->message, $event->target);
        if (!isset($event->matches['terms'])) {
            $this->respondWithPrefix('You need to specify search terms.');
            return;
        }
        $filters = (isset($event->matches['filter']))
                    ? ($event->matches['filter'])
                          : explode(',', $event->matches['filter'])
                          ? array()
                    : array();
        if (isset($event->matches['engine']) && $event->matches['engine']) {
            try {
                $results = SearchEngineHandler::searchWithEngine(
                    $event->matches['engine'], $event->matches['terms'],
                    1, $filters
                );
            } catch (\Exception $e) {
                $this->respondWithPrefix($e->getMessage());
                return;
            }
        } else {
            $results = SearchEngineHandler::search(
                $event->matches['terms'], 1, $filters
            );
        }
        if (!$results) {
            $this->respondWithPrefix('Could not find any results.');
            return;
        }
        $entry = $results->entries[0];
        $this->respondWithInformation($this->_createResponseData($entry));
    }
    
    private function _createResponseData($entry)
    {
        $math = new \Botlife\Utility\Math;
        $data = array();
        if (isset($entry->duration)) {
            $data['Title'] = array();
            $data['Title'][] = $entry->title;
            $data['Title'][] = array(gmdate('H:i:s', $entry->duration));
        } else {
            $data['Title'] = $entry->title;
        }
        if (isset($entry->price)) {
            $data['Price'] = $math->alphaRound($entry->price->amount, 2);
            if (isset($entry->price->currency)) {
                $data['Price'] .= ' ' . $entry->price->currency;
            }
        }
        if (isset($entry->rating)) {
            if (!isset($entry->rating->likes)) {
                $data['Rating'] = \DataGetter::getData(
                	'star-rating', $entry->rating->average / 20
                );
            } else {
                $data['Rating'] = array();
                $data['Rating'][] = \DataGetter::getData(
                	'star-rating', $entry->rating->average / 20
                );
                $data['Rating'][] = array(
                    'Likes'    => number_format($entry->rating->likes),
                    'Dislikes' => number_format($entry->rating->dislikes),
                );
            }
        }
        if (isset($entry->date)) {
            $data['Date'] = $entry->date->format('Y-m-d');
        }
        if (isset($entry->prototype)) {
            $data['Prototype'] = $entry->prototype;
        }
        if (isset($entry->categories)
            && !(isset($entry->prototype) && strlen($entry->prototype) > 50)
        ) {
            $name = (count($entry->categories) == 1) ? 'Category' : 'Categories';
            $data[$name] = implode(', ', $entry->categories);
        }
        if (isset($entry->description)) {
            if (isset($entry->prototype) && strlen($entry->prototype) > 50) {
                $length = 50;
            } else {
                $length = 100;
            }
            if (strlen($entry->description) > $length) {
                $entry->description = substr(
                    $entry->description, 0, $length
                ) . '...';
            }
            $data['Description'] = $entry->description;
        }
        if (isset($entry->url)) {
            if (strlen($entry->url) > 50) {
                $shortened = \DataGetter::getData('url-shortener', $entry->url);
                if ($shortened) {
                    $entry->url = $shortened;
                }
            }
            $data['Link'] = $entry->url;
        }
        return $data;
    }
    
}
