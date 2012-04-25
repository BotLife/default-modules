<?php

namespace Botlife\Module\BitTorrent\Command;

use Botlife\Application\Colors;
use Ircbot\Type\MessageCommand;

class TorrentConvert extends \Botlife\Command\ACommand
{

    public $regex  = '/.*\.torrent$/i';
    public $action = 'run';
    public $code   = 'torrent-convert';
    
    public $responseType    = self::RESPONSE_PUBLIC;
    
    public function run(MessageCommand $event)
    {
        $this->detectResponseType($event->message, $event->target);
        $C = new Colors;
        $data = @\DataGetter::getData('file-content', $event->message);
        if (!$data) {
            return;
        }
        try {
            $torrent = \PHP\BitTorrent\Torrent::createFromData($data);
        } catch (InvalidArgumentException $e) {
            return;
        }
        $this->respondWithInformation(array(
        	'Magnet URI' => $C(Colors::STYLE_NORMAL, $torrent->getMagnet()))
        );
    }

}
    