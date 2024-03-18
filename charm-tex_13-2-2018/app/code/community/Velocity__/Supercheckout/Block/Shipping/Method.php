<?php

class Velocity_Supercheckout_Block_Shipping_Method extends Mage_Checkout_Block_Onepage_Abstract
{
    protected $_rates;
    protected $_address;
    
    protected function _construct()
    {
        $this->getCheckout()->setStepData('shipping_method', array(
            'label'     => Mage::helper('checkout')->__('Shipping Method'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
    }    
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }
    public function getShippingRates()
    {

        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();

            $groups = $this->getAddress()->getGroupedAllShippingRates();
            

            return $this->_rates = $groups;
        }

        return $this->_rates;
    }

    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }

    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    public function getShippingPrice($price, $flag)
    {
        return $this->getQuote()->getStore()->convertPrice(Mage::helper('tax')->getShippingPrice($price, $flag, $this->getAddress()), true);
    }
    
}
