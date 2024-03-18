<?php
/*------------------------------------------------------------------------
 # SM Market - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

//$compress_css_js = Mage::getStoreConfig('market_cfg/advanced/compress_css_js');	

class Sm_Market_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{
	private $mediaBaseUrl;
	private $mediaBasePath;
	
	public function __construct() {
		parent::__construct();
		$this->mediaBaseUrl = Mage_Core_Model_Store::URL_TYPE_MEDIA . '/extendware/ewcore/generated';
    	$this->mediaBasePath = 'extendware/ewcore/generated';
	}

	public function addItem($type, $name, $params=null, $if=null, $cond=null)
    {
    	$key = $type.'/'.$name;
    	$existedBefore = isset($this->_data['items'][$key]);
    	parent::addItem($type, $name, $params, $if, $cond);

    	list($newKey, $newItem) = $this->rewriteItemForDynamicContent($key);
        if ($existedBefore === false) {
        	unset($this->_data['items'][$key]);
        	$this->_data['items'][$newKey] = $newItem;
        } else {
        	$items = array();
        	foreach ($this->_data['items'] as $k => $v) {
        		if ($k == $key) {
        			$k = $newKey;
        			$v = $newItem;
        		}
        		$items[$k] = $v;
        	}
        	$this->_data['items'] = $items;
        }
        return $this;
    }
    
    private function rewriteItemForDynamicContent($key) {
    	if (!isset($this->_data['items'][$key])) return null;
    	$item = $this->_data['items'][$key];
    	
    	$newKey = $key;
	    switch ($item['type']) {
			case 'ewgenerated_js':
				$block = $this->getLayout()->createBlock($item['name']);
				if ($block) {
					$newItemType = 'js';
					$newItemName = $this->mediaBaseUrl . '/js/' . $block->getFilename();
					$newKey = $newItemType. '/' . $newItemName;
					$item['type'] = $newItemType;
					$item['name'] = $newItemName;
					$item['filepath'] = $block->getCachedFilePath();
					$item['original'] = $item;
				}
				break;
			case 'ewgenerated_css':
				$block = $this->getLayout()->createBlock($item['name']);
				if ($block) {
					$newItemType = 'js_css';
					$newItemName = $this->mediaBaseUrl . '/css/' . $block->getFilename();
					$newKey = $newItemType. '/' . $newItemName;
					$item['type'] = $newItemType;
					$item['name'] = $newItemName;
					$item['filepath'] = $block->getCachedFilePath();
					$item['original'] = $item;
					
					if (!@$item['params']) {
						$item['params'] = 'media="all"';
					}
				}
				break;
		}
    	return array($newKey, $item);
    }
    
	public function getCssJsHtml()
	{
		$items = array(); // this is used to keep the order the same
		if (!isset($this->_data['items']) or !is_array($this->_data['items'])) $this->_data['items'] = array();
		foreach ($this->_data['items'] as $key => $item) {
			list ($newKey, $newItem) = $this->rewriteItemForDynamicContent($key);
			$items[$newKey] = $newItem;
		}
		$this->_data['items'] = $items;
		return $this->rewriteUrlsInString(parent::getCssJsHtml());
	}
	
	public function mergeJsFiles($files) 
	{
		foreach ($files as &$file) {
			if (strpos($file, $this->mediaBasePath)) {
				$file = Mage::getConfig()->getOptions()->getMediaDir() . DS . 'extendware' . DS . 'ewcore' . DS . 'generated' . DS . preg_replace('/.+?' . preg_quote($this->mediaBasePath, '/') . '/', '', $file);
			}
			unset($file); // cleanup reference
		}
		return Mage::getDesign()->getMergedJsUrl($files);
	}
	
	public function mergeCssFiles($files) 
	{
		foreach ($files as &$file) {
			if (strpos($file, $this->mediaBasePath)) {
				$file = Mage::getConfig()->getOptions()->getMediaDir() . DS . 'extendware' . DS . 'ewcore' . DS . 'generated' . DS . preg_replace('/.+?' . preg_quote($this->mediaBasePath, '/') . '/', '', $file);
			}
			unset($file); // cleanup reference
		}

		return Mage::getDesign()->getMergedCssUrl($files);
	}
	
	protected function &_prepareStaticAndSkinElements($format, array $staticItems, array $skinItems, $mergeCallback = null)
    {
    	if (strpos($format, 'type="text/css"') !== false) {
    		if (Mage::getStoreConfigFlag('dev/css/merge_css_files')) {
    			$mergeCallback = array($this, 'mergeCssFiles');
    		}
    	} elseif (strpos($format, 'type="text/javascript"') !== false) {
    		if (Mage::getStoreConfigFlag('dev/js/merge_files')) {
    			$mergeCallback = array($this, 'mergeJsFiles');
    		}
    	}
    	
    	$html = parent::_prepareStaticAndSkinElements($format, $staticItems, $skinItems, $mergeCallback);
    	$html = $this->rewriteUrlsInString($html);
        return $html;
    }
    
    protected function rewriteUrlsInString($string)
    {
    	return str_replace('js/' . $this->mediaBaseUrl, $this->mediaBaseUrl, $string);
    }
}

