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
 
class Anowave_Price_Helper_Data extends Anowave_Package_Helper_Data
{
	/**
     * Package Stock Keeping Unit
     *
     * @var string
     */
	protected $package = 'MAGE-PRICE';
	
	/**
	 * Package Config
	 * 
	 * @var string
	 */
	protected $config = 'price/settings/license';
	
	/**
	 * Get initial discount settings
	 * 
	 * return []
	 */
	public function getInitialDiscountConfig()
	{
		return (object) array
		(
			'apply' 				=> (int) Mage::getStoreConfig('price/initial/apply'),
			'discount_type' 		=> 		 Mage::getStoreConfig('price/initial/discount_type'),
			'discount_fixed' 		=> 		 Mage::getStoreConfig('price/initial/dicount_fixed'),
			'discount_percentage' 	=> 		 Mage::getStoreConfig('price/initial/dicount_percentage'),
			'discount_categories' 	=> array_map('trim', explode(chr(44),Mage::getStoreConfig('price/initial/discount_categories')))
		);
	}
	
	/**
	 * Simple pricing
	 * 
	 * @todo
	 */
	public function useSimplePricing()
	{
		if (1)
		{
			return false;	
		}
		
		return 1 === (int) Mage::getStoreConfig('price/configurable/use_variant_simple_price');
	}
}