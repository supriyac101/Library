<?php
class Velocity_Supercheckout_Helper_Url extends Mage_Checkout_Helper_Url
{    
    public function getCheckoutUrl()
    {
    	if(Mage::helper('supercheckout')->isSupercheckoutEnabled())
        	return $this->_getUrl('supercheckout', array('_secure'=>true));
        else
        	return parent::getCheckoutUrl();
    }
}
