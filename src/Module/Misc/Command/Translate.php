<?php

namespace Botlife\Module\Misc\Command;

use Botlife\Module\Misc\Dao\Translator;

class Translate extends \Botlife\Command\ACommand
{

    public $regex = array(
        '/^[.@!]translate(?P<langs> (?P<from>[a-zA-Z]{2})(\|| )(?P<to>[a-zA-Z]{2}))?( (?P<text>.*))?$/i',
    );
    public $action = 'translate';
    public $code   = 'translate';
    
    // public $languages = array('nl', 'en', 'ru');

    public function translate($event)
    {
        $startTime = microtime(true);
        $this->detectResponseType($event->message, $event->target);
        $c = new \Botlife\Application\Colors;
        if (!isset($event->matches['langs'])) {
            $this->respondWithPrefix(
                $c(12, 'You need to specify two languages. For example: ')
                    . $c(3, '!translate nl en Hoe gaat het?')
            );  
            return;
        }
        if (!isset($event->matches['text'])) {
            $this->respondWithPrefix(
                $c(12, 'You need to specify a text. For example: ')
                    . $c(3, '!translate nl en Hoe gaat het?')
            );  
            return;
        }
        /*if (!in_array(strtolower($event->matches['from']), $this->languages)) {
            $this->respondWithPrefix(
                'The language you\'re trying to translate from isn\'t supported'
            );
            return;
        }
        if (!in_array(strtolower($event->matches['to']), $this->languages)) {
            $this->respondWithPrefix(
                'The language you\'re trying to translate to isn\'t supported'
            );
            return;
        }*/
        $info = \DataGetter::getData('translator',
            Translator::languageId($event->matches['from']),
            Translator::languageId($event->matches['to']),
            $event->matches['text']
        );
        if ($info === false) {
            $this->respondWithPrefix('Could not translate your text');
            return;
        }
        $this->respondWithInformation(array(
            'Origin'     => array(
                $event->matches['text'],
                array(
                    Translator::languageName($info->from) . ' => '
                    	. Translator::languageName($info->to)
            	)
            ),
            'Translated' => $info->text,
            'Time'       => round((microtime(true) - $startTime) * 1000, 2)
                . 'ms'
        ));
    }
    
}
