<?php

namespace Botlife\Module\Misc\Dao;

class DoCurl
{
    
    private $_curl;
    
    public function doCurl($url)
    {
        if (!$this->_curl) {
            $this->_curl = curl_init();
            curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
        }
        if ($proxy = $this->_getProxy()) {
            curl_setopt($this->_curl, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($this->_curl, CURLOPT_URL, $url);
        return curl_exec($this->_curl);
    }
    
    private function _getProxy()
    {
        $context = stream_context_get_options(stream_context_get_default());
        if ($context['http']['proxy']) {
            return str_replace('tcp', 'http', $context['http']['proxy']);   
        }
        return false;
    }
    
}