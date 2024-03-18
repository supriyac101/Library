<?php
class Velocity_Supercheckout_Block_Links extends Mage_Checkout_Block_Links
{
    public function addCheckoutLink()
    {
		if ($this->helper('supercheckout')->isSupercheckoutEnabled()) 
        {
        	$parent = $this->getParentBlock();
			if ($parent)
            	$parent->addLink($this->helper('supercheckout')->__('Checkout'), 'supercheckout', $this->helper('supercheckout')->__('Checkout'), true, array('_secure'=> true), 60, null, 'class="top-link-checkout"');

			return $this;
        }
        else
            return parent::addCheckoutLink();
    }
}