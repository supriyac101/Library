<?php

class Extendware_EWPRibbon_Block_Code extends Extendware_EWCore_Block_Mage_Core_Template
{
    public function _construct()
    {
    	parent::_construct();
        $this->setTemplate('extendware/ewpribbon/code.phtml');
    }
    
    public function getConfigTemplates() {
    	$configTemplates = array();
    	
    	$sources = array('category_ribbons' => $this->getCategoryRibbons(), 'product_ribbons' => $this->getProductRibbons());
    	foreach ($sources as $sourceKey => $source) {
	    	foreach ($source as &$ribbons) {
	    		foreach ($ribbons as &$ribbon) {
		    		$params = '';
		    		if (is_array($this->getRefSelectors())) {
		    			$ribbon['ref_selectors'] = $this->getRefSelectors();
		    		}
		    		$values = array(
		    			json_encode($ribbon['id']), 
		    			json_encode($ribbon['text']), 
		    			json_encode($ribbon['image_path']), 
		    			json_encode($ribbon['valign']), 
		    			json_encode($ribbon['align']), 
		    			json_encode($ribbon['text_style']), 
		    			json_encode($ribbon['image_style']), 
		    			json_encode($ribbon['container_style']), 
		    			json_encode($ribbon['inner_container_style']),
		    			json_encode($ribbon['ref_selectors']),
		    			json_encode($this->getDataSetDefault($sourceKey . '_context_selector', 'body')),
		    		);
		    		$params = join(',', $values);
		    		$key = substr(md5($params), 0, 8);
		    		$configTemplates[$key] = array(
		    			'params' => $params
		    		);
		    		$ribbon['config_template_key'] = $key;
		    		unset($ribbon);
	    		}
	    	}
	    	$this->setData($sourceKey, $source);
    	}
    	return $configTemplates;
    }
    
    public function toRefSelectorString(array $array) {
    	$string = '';
		foreach ($array as $element) {
			if ($string) $string .= ',';
			$string .= "'" . $element . "'";
		}

		return $string;
    }
    
    public function getRefSelectors() {
    	if ($this->hasData('ref_selectors')) {
    		return (array)$this->getData('ref_selectors');
    	}
    	return null;
    }
    public function getWrapInDomLoadedEvent() {
    	return (bool) $this->getData('wrap_in_dom_loaded_event');
    }
    
    public function getProductCollection() {
    	$collection = Mage::getSingleton('ewpribbon/storage')->getProductCollection();
    	if (!$collection) $collection = Mage::registry('ew:current_product_collection');
    	return $collection;
    }
    
    public function hasProductCollection() {
    	return (bool)$this->getProductCollection();
    }

	public function getProduct() {
		$product = Mage::getSingleton('ewpribbon/storage')->getProduct();
		if (!$product) $product = Mage::registry('ew:current_product');
    	return $product;
    }
    
    public function hasProduct() {
    	return (bool)$this->getProduct();
    }
    
    public function getProductRibbons() {
    	if (!$this->hasProductRibbons()) {
    		$this->setProductRibbons($this->mHelper()->getMatchingProductPageRibbons($this->getProduct()));
    	}
    	
    	return $this->getData('product_ribbons');
    }
    
	public function getCategoryRibbons() {
    	if (!$this->hasCategoryRibbons()) {
    		$this->setCategoryRibbons($this->mHelper()->getMatchingCategoryPageRibbons($this->getProductCollection()));
    	}
    	
    	return $this->getData('category_ribbons');
    }
    
    public function hasRibbons() {
    	$productRibbons = $this->getProductRibbons();
    	$categoryRibbons = $this->getCategoryRibbons();
    	return !empty($productRibbons) or !empty($categoryRibbons);
    }
    
    public function _toHtml() 
    {
    	if ($this->mHelper('config')->isEnabled() and $this->hasRibbons()) {
    		return parent::_toHtml();
    	}
    	
    	return '';
    }
}
