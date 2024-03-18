<?php

class Velocity_Supercheckout_Model_Register extends Velocity_Supercheckout_Model_Validator
{    
    public function _construct() 
    {
        parent::_construct();
        
        $this->_result = '';
        $this->_userId = -1;
        
        if ($_POST['licence'] == 'ok')
        {
            $this->setEmail($_POST['email']);
            
            if ($this->isEmailExist())
            {
                $this->_result .=  'emailisexist,';
            }
            else
            {
                $this->setPassword($_POST['password'], $_POST['passwordsecond']);
                $this->setName($_POST['firstname'], $_POST['lastname']);
                $this->setNewsletter($_POST['newsletter']);
        
                if ($this->_result == '')
                {
                    $this->registerUser();
                    
                    if ($this->_userNewsletter == true)
                    {
                        $this->subscribeUser();
                    }
                }
            }
        }
        else
        {
            $this->_result = 'nolicence,';
        }        
    }
    
    private function registerUser()
    {
        $customer = Mage::getModel('customer/customer');
        
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        
        $customer->setEmail($this->_userEmail);
        $customer->setPassword($this->_userPassword);
        $customer->setFirstname($this->_userFirstName);
        $customer->setLastname($this->_userLastName);
        
        try
        {
            $customer->save();
            $customer->setConfirmation(null);
            $customer->save();
            
            $storeId = $customer->getSendemailStoreId();
            $customer->sendNewAccountEmail('registered', '', $storeId);
            
            Mage::getSingleton('customer/session')->loginById($customer->getId());
            
            $this->_userId = $customer->getId();
            
            $this->_result = 'success';
        }
        catch (Exception $ex)
        {
            //Zend_Debug::dump($ex->getMessage());
            //throw new Exception($ex->getMessage());
            $this->_result .= 'frontendhackerror,';
        }
    }
    
    private function subscribeUser()
    {
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($this->_userEmail);

        if (!$subscriber->getId())
        {
            $subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
            $subscriber->setSubscriberEmail($this->_userEmail);
            $subscriber->setSubscriberConfirmCode($subscriber->RandomSequence());
        }

        $subscriber->setStoreId(Mage::app()->getStore()->getId());
        $subscriber->setCustomerId($this->_userId);
        
        try
        {
            $subscriber->save();
        }
        catch (Exception $ex)
        {
            //throw new Exception($ex->getMessage());
        }


    }
}

?>
