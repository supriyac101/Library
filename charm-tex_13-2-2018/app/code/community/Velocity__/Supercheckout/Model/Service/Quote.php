<?php
class Velocity_Supercheckout_Model_Service_Quote extends Mage_Sales_Model_Service_Quote
{
    protected function _validate()
    {
        $helper = Mage::helper('supercheckout');
        if (!$this->getQuote()->isVirtual())
        {
            
            $address = $this->getQuote()->getShippingAddress();
            
            $addrValidator = Mage::getSingleton('supercheckout/type_gather')->validateAddress($address);
            
            
            if ($addrValidator !== true){
                Mage::throwException($helper->__('Please check shipping address information. %s', implode(' ', $addrValidator)));
            }

            $ship_method = $address->getShippingMethod();
            $rate = $address->getShippingRateByCode($ship_method);
            if (!$this->getQuote()->isVirtual() && (!$ship_method || !$rate)){
                /*Start Code Modified By Raghubendra Singh on 06-Jan-2015 to enable shipping method validation only if all the products in the cart are not virtual or downloadable*/
                        if (Mage::getSingleton('checkout/session')->getShowShipping() == 1)
                        {
                                $shipping_error = array('shipping_method_error',$helper->__('Please specify a shipping method.'));
                                Mage::throwException($helper->__('shipping_method_required_error'));
                        }
                /*End Code Modified By Raghubendra Singh on 06-Jan-2015 to enable shipping method validation only if all the products in the cart are not virtual or downloadable*/
            }
            
        }
        $addrValidator = Mage::getSingleton('supercheckout/type_gather')->validateAddress($this->getQuote()->getBillingAddress());

        if ($addrValidator !== true){
            
            Mage::throwException($helper->__('Please check billing address information. %s', implode(' ', $addrValidator)));
        }

        if (!($this->getQuote()->getPayment()->getMethod())){
            
			Mage::throwException($helper->__('payment_method_required_error'));
        }

        return $this;
    }
}
