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
 
class Anowave_Price_Model_Product_Type_Configurable_Price extends Mage_Catalog_Model_Product_Type_Configurable_Price 
{
	/**
	 * Get product final price
	 *
	 * @param   double $qty
	 * @param   Mage_Catalog_Model_Product $product
	 * @return  double
	 */
	public function getFinalPrice($qty=null, $product)
	{
		if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) 
		{
			return $product->getCalculatedFinalPrice();
		}
	
		$basePrice = $this->getBasePrice($product, $qty);
		$finalPrice = $basePrice;
		$product->setFinalPrice($finalPrice);
		
		Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => $qty));
		
		$finalPrice = $product->getData('final_price');
		
		/**
		 * Use simple price
		 */
		if (!Mage::helper('price')->useSimplePricing())
		{
			$finalPrice += $this->getTotalConfigurableItemsPrice($product, $finalPrice);
			$finalPrice += $this->_applyOptionsPrice($product, $qty, $basePrice) - $basePrice;
		}
		
		$finalPrice = max(0, $finalPrice);
	
		$product->setFinalPrice($finalPrice);
		
		return $finalPrice;
	}
}