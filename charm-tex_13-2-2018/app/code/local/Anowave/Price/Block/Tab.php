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
 
class Anowave_Price_Block_Tab extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        $this->setTemplate('price/tab.phtml');
    }
    
    public function getCustomtabInfo()
    {
		return $this->__('Custom prices');
	}

	/**
	* Return Tab label
	*
	* @return string
	*/
    public function getTabLabel()
    {
        return $this->__('Custom prices');
    }

	/**
	* Return Tab title
	*
	* @return string
	*/
    public function getTabTitle()
    {
        return $this->__('Custom prices');
    }

	/**
	* Can show tab in tabs
	*
	* @return boolean
	*/
    public function canShowTab()
    {
       return Mage::helper('price')->legit();
    }

	/**
	* Tab is hidden
	*
	* @return boolean
	*/
    public function isHidden()
    {
        return false;
    }

	/**
	* Defines after which tab, this tab should be rendered
	*
	* @return string
	*/
    public function getAfter()
    {
        return 'tags';
    }
}