<?php
class Extendware_EWPRibbon_Block_Js_General extends Extendware_EWCore_Block_Generated_Js
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('extendware/ewpribbon/js/general.phtml');
    }
    
	
	public function getCacheKey() {
        $key = parent::getCacheKey();
        $key .= $this->mHelper('config')->getHash();
        
        return md5($key);
	}
	
	public function isDebuggingEnabled() {
		return $this->mHelper('config')->isDebuggingEnabled();
	}
	
	public function getDebugMessageJs($template, $message, $type = 'default') {
		if ($this->isDebuggingEnabled() === false) return '';
		$debug = sprintf('this.debug(%s, %s);', json_encode($message), json_encode($type));
		return sprintf($template, $debug);
	}
}

