<?php

class Velocity_Supercheckout_Block_Checkoutbutton extends Mage_Core_Block_Template
{
    public function isEnabled()
    {
//        echo $this->helper('supercheckout')->isSupercheckoutEnabled();
        return $this->helper('supercheckout')->isSupercheckoutEnabled();
    }

    public function checkAllowed()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }

    public function getSupercheckoutUrl()
    {
    	$url	= $this->getUrl('supercheckout', array('_secure' => true));
        return $url;
    }
}
