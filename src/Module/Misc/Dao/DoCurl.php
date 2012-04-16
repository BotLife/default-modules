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
        curl_setopt_array($this->_curl, $this->_getOptions());
        curl_setopt($this->_curl, CURLOPT_URL, $url);
        return curl_exec($this->_curl);
    }
    
    private function _getOptions()
    {
        $context = stream_context_get_options(stream_context_get_default());
        $options = array();
        if (isset($context['http']['proxy'])) {
            $options[CURLOPT_PROXY] = str_replace(
            	'tcp', 'http', $context['http']['proxy']
            ); 
        }
        if (isset($context['http']['timeout'])) {
            $options[CURLOPT_TIMEOUT] = (int) $context['http']['timeout'];
        }
        return $options;
    }
    
}