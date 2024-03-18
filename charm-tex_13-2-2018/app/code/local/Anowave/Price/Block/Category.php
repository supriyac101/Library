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
 
class Anowave_Price_Block_Category extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('price/category.phtml');
    }
    
    public function getCategories()
    {
    	$categories = array();
    	
    	foreach (Mage::getModel('price/price_category')->getCollection()->addFieldToFilter('price_id', $this->getRow()->getPriceId()) as $category)
    	{
    		$model = Mage::getModel('catalog/category')->load
    		(
    			$category->getPriceCategoryId()
    		);
    		
    		$categories[] = $model->getName();
    	}
    	
    	return $categories;
    }
}