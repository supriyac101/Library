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
 
class Anowave_Price_Block_Finder extends Varien_Data_Form_Element_Abstract 
{
	/**
	* Retrieve Element HTML fragment
	*
	* @return string
	*/
	public function getElementHtml()
	{
		$disabled = false;
		
		if (!$this->getValue()) 
		{
			$this->setData('disabled', 'disabled');
			$disabled = true;
		}
		
		$block = Mage::app()->getLayout()->createBlock('adminhtml/widget_button')->setData(array
		(
			'id'		=> 'chooser',
            'label'     => Mage::helper('core')->__('Select product'),
			'title' 	=> Mage::helper('core')->__('Select product'),
            'onclick'   => 'Price.choose(this)',
            'class'		=> 'back'
        ));
        
		return '<div class="customer-pricing">' . $block->toHtml() . '<div id="chooseGrid"></div></div>';
	}
}