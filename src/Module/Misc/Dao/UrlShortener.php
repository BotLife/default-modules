<?php

namespace Botlife\Module\Misc\Dao;

class UrlShortener
{
    
    public function usingGoogle($link)
    {
        $url = 'https://www.googleapis.com/urlshortener/v1/url';
        $parameters = array(
            'longUrl' => $link,
            'key'     => 'AIzaSyBYJv5tEe_VdwUt4Quz0uCT2xWzbVYbptw'
        );
        $data = \DataGetter::getData(
        	'file-content', $url, json_encode($parameters), 'application/json'
        );
        if (!$data) {
            return false;
        }
        $data = json_decode($data);
        if (!isset($data->id)) {
            return false;
        }
        return $data->id;
    }
    
}