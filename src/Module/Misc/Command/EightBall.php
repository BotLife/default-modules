<?php

namespace Botlife\Module\Misc\Command;

class EightBall extends \Botlife\Command\ACommand
{

    public $regex  = '/^[.!@]8(ball)?( (?P<question>.*))?$/i';
    public $action = 'run';
    public $code   = '8ball';
    
    public $answers = array(
        'Yes!',
        'No....',
        'Maybe..',
        'You wish',
    );
    
    public function run($event)
    {
        $this->detectResponseType($event->message, $event->target);
        $c = new \Botlife\Application\Colors;
        
        if (!isset($event->matches['question'])) {
            $this->respond(
                $c(12, '[') . $c(3, '8BALL') . $c(12, '] ')
                    . $c(12, 'You need to specify a question. For example: ')
                    . $c(3, '!8ball Should a buy a rune chestplate?')
            );  
            return;
        }
        $question = $event->matches['question'];
        $answer   = $this->getAnswer();
        
        $this->respondWithInformation(array(
            'Question' => $question,
            'Answer'   => $answer,
        ));
    }
    
    public function getAnswer()
    {
        return $this->answers[array_rand($this->answers)];
    }

}
