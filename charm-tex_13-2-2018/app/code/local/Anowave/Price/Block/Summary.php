<?php
/**
 * Anowave Magento Price Per Customer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Price
 * @copyright 	Copyright (c) 2016 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 
class Anowave_Price_Block_Summary extends Mage_Core_Block_Template
{
	public function getSummary()
	{
	    $product = Mage::registry('current_product');
	    
	    if ($product && Mage::getSingleton('customer/session')->isLoggedIn())
	    {
	        return $this->render($product, Mage::getSingleton('customer/session')->getCustomer());
	    }
	    
	    return array();
	}
	
	public function getCustomer()
	{
	    return Mage::getSingleton('customer/session')->getCustomer();
	}
	
	private function render(Mage_Catalog_Model_Product $product, Mage_Customer_Model_Customer $customer)
	{
		if (!$product->getOriginalPrice())
		{
			return array();
		}
		
	    $model = Mage::getModel('price/observer');
	    
	    $prices = $model->getCustomerPrice($product, $customer, null, false);
	    
	    foreach ($prices as &$price)
	    {
	        $final = $model->getFinalPrice($product, $customer, @$price['price_tier_quantity']);
	        
	        $price['percent_save'] = number_format((int) 100 - (($final/$product->getOriginalPrice()) * 100),2);
	        $price['final_price']  = Mage::helper('core')->currency($final, true, false);
	        
	    }
	    
	    unset($price);

	    $collection = array
	    (
	    	'main' => array(),
	    	'tier' => array()
	    );
	    
	    foreach ($prices as $price)
	    {
	    	if (0 < (int) @$price['price_tier_quantity'])
	    	{
	    		$collection['tier'][$price['price_id']] = $price;
	    	}
	    	else 
	    	{
	    		$collection['main'][$price['price_id']] = $price;
	    	}
	    }
	    
	    usort($collection['tier'], array($this,'sort'));
	    
	    return array_filter($collection);
	}
	
	public function sort($a, $b)
	{
		return @$a['price_tier_quantity'] > @$b['price_tier_quantity'];
	}
}