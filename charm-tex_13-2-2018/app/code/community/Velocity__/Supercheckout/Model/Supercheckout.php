<?php

class Velocity_Supercheckout_Model_Supercheckout extends Mage_Core_Model_Abstract {
    public function _construct(){
        
        $this->_init('velocity/supercheckout');
    }
    private function loginUser(){
        $session = Mage::getSingleton('customer/session');

        try
        {
            $session->login($this->_userEmail, $this->_userPassword);
            $customer = $session->getCustomer();
            
            $session->setCustomerAsLoggedIn($customer);
            
            $this->_result .= 'success';
        }
        catch(Exception $ex)
        {
            $this->_result .= 'wronglogin,';
        }
    }    
    public function getResult(){
        return $this->_result;
    }
    
}