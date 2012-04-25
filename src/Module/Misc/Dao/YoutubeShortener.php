<?php

namespace Botlife\Module\Misc\Dao;

class YoutubeShortener
{
    
    public function shorten($link)
    {
        $regex = '/(http\:\/\/)?(www\.)?(youtube\.com\/watch\?(.*)?v\='
        	. '(?P<idlong>[A-Za-z0-9_-]+)(\&(.*))?|youtu\.be\/(?P<id>'
    		. '[A-Za-z0-9_-]+))/';
        if (!preg_match($regex, $link, $matches)) {
            return false;
        }
        return sprintf(
        	'http://youtu.be/%s',
            (isset($matches['id'])) ? $matches['id'] : $matches['idlong']
        );
    }
    
}