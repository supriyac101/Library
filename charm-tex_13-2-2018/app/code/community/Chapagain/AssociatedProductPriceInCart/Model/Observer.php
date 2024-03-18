<?php
 
class Chapagain_AssociatedProductPriceInCart_Model_Observer
{
    public function updateCartPrice(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();        
        $quoteItem = $event->getQuoteItem();
        //$product = $item->getProduct();        
		
		if ($option = $quoteItem->getOptionByCode('simple_product')) {
			$simpleProductId = $option->getProduct()->getId();
			$simpleProduct = Mage::getModel('catalog/product')->load($simpleProductId);
			$simpleProductPrice = $simpleProduct->getFinalPrice();
			
			$quoteItem->setOriginalCustomPrice($simpleProductPrice);
			
			// No need to save quote item object in observer.
			// It will save the object automatically, as it is being passed by reference.
			
			//$quoteItem->save();
		}		
    }    
}
