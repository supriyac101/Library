<?php

class Velocity_Supercheckout_Helper_Data extends Mage_Core_Helper_Abstract {
    private $_settings = array();
    protected $_agree = null;
    public function setSettings(){
        $check = Mage::getStoreConfig('checkout/supercheckout/settings',Mage::app()->getStore()->getId());
        if(isset($check)){
            $this->_settings = unserialize($check);
            return true;
        }else{
            $this->_settings = array();
            return false;
        }
    }
    public function isSupercheckoutEnabled(){
        if($this->setSettings()){
            return (bool) $this->_settings['supercheckout']['general']['enable'];
        }else{
            return false;
        }
        
    }
    public function getSettings(){
        
        $this->setSettings();
        return $this->_settings['supercheckout'];
    }
    public function loggedIn(){
        return $logged_in = Mage::getSingleton( 'customer/session' )->isLoggedIn();
    }
    public function getSelectedShippingMethod(){
        $shipping_method_details = Mage::getSingleton('supercheckout/supercheckout')->getData('shipping_method_details');
        $shipping_method_string = json_encode($shipping_method_details);
        $shipping_method_session = Mage::getSingleton('supercheckout/supercheckout')->getData('shipping_method_session');
        $shipping_method_default = isset($this->_settings['supercheckout']['step']['shipping_method']['default_option'])?$this->_settings['supercheckout']['step']['shipping_method']['default_option']:"";
        if($shipping_method_session != ""){
            if(strstr($shipping_method_string, $shipping_method_session)){
                $selected_shipping_method = $shipping_method_session;
            }else{
                $selected_shipping_method = isset($shipping_method_details[0]['value'])?$shipping_method_details[0]['value']:"";
            }
        }elseif($shipping_method_default != ""){
            if(strstr($shipping_method_string, $shipping_method_default)){
                $selected_shipping_method = $shipping_method_default;
            }else{
                $selected_shipping_method = isset($shipping_method_details[0]['value'])?$shipping_method_details[0]['value']:"";
            }
        }else{
            $selected_shipping_method = ""; //you can disable shipping method div->hide here
        }
        return $selected_shipping_method;
    }
    public function setSelectedShippingMethod(){
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        if (Mage::getSingleton('checkout/session')->getShowShipping() == 1)             // Code Added by Raghubendra Singh on 06-Jan-2015 to save shipping method only if all the products are not virtual or downloadable
        {
                $selected_shipping_method = $this->getSelectedShippingMethod();
                $quote->getShippingAddress()->setShippingMethod($selected_shipping_method)->save();
        }
        $quote->collectTotals()->save();
    }
    public function getShippingMethodDefault(){
        $shipping_method_default = isset($this->_settings['supercheckout']['step']['shipping_method']['default_option'])?$this->_settings['supercheckout']['step']['shipping_method']['default_option']:"";
        return $shipping_method_default;
    }
    public function getAgreeIds()
    {
        $this->getSettings();
        if (is_null($this->_agree)){
            if ($this->_settings['supercheckout']['option']['guest']['confirm']['fields']['agree']['display'] || $this->_settings['supercheckout']['option']['logged']['confirm']['fields']['agree']['display']){
                $this->_agree = Mage::getModel('checkout/agreement')
                                                                    ->getCollection()
                                                                    ->addStoreFilter(Mage::app()->getStore()->getId())
                                                                    ->addFieldToFilter('is_active', 1)
                                                                    ->getAllIds();
            }
            else{
            	$this->_agree = array();
            }
        }
        return $this->_agree;
    }
    public function getMagentoVersion(){
		$ver_info = Mage::getVersionInfo();
		$mag_version	= "{$ver_info['major']}.{$ver_info['minor']}.{$ver_info['revision']}.{$ver_info['patch']}";
		
		return $mag_version;
    } 
    public function isGuestCheckoutAllowed(Mage_Sales_Model_Quote $quote, $store = null){
        if ($store === null) {
            $store = $quote->getStoreId();
        }
        $setting = $this->getSettings();
        $guestCheckout = $setting['step']['login']['option']['guest']['display'];
        
        if ($guestCheckout == true) {
            $result = new Varien_Object();
            $result->setIsAllowed($guestCheckout);
            Mage::dispatchEvent('checkout_allow_guest', array(
                'quote'  => $quote,
                'store'  => $store,
                'result' => $result
            ));

            $guestCheckout = $result->getIsAllowed();
        }

        return $guestCheckout;
    }
}