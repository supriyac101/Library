<?php

class Extendware_EWPRibbon_Model_Observer {
	static function catalogControllerProductViewEvent(Varien_Object $observer)
	{
		$product = $observer->getProduct();
		$storage = Mage::getSingleton('ewpribbon/storage');
		if ($product and !$storage->getProduct()) {
			$storage->setProduct($product);
    	}
	}
	
	static public function catalogBlockProductListCollection(Varien_Event_Observer $observer)
	{
		$collection = $observer->getEvent()->getCollection();
		$storage = Mage::getSingleton('ewpribbon/storage');
		if (!$storage->getProductCollection()) {
			$storage->setProductCollection($collection);
    	}
	}
}