<?php
/** 
 * @category Brainvire 
 * @package Brainvire_Ordercomment 
 * @copyright Copyright (c) 2015 Brainvire Infotech Pvt Ltd
 */
class Brainvire_Ordercomment_Block_Onepage_Ordercomment extends Mage_Checkout_Block_Onepage_Abstract
{
	/**
     * Calling the order comment in checkout step
     */
    protected function _construct()
    {    	
        $this->getCheckout()->setStepData('ordercomment', array(
            'label'     => Mage::helper('checkout')->__('Order Comment'),
            'is_show'   => true
        ));
        
        parent::_construct();
    }
}