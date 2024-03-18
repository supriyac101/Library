<?php


class Velocity_Supercheckout_Model_Validator extends Varien_Object
{    
    protected $_userEmail;
    
    protected $_userPassword;
    
    protected $_userFirstName;
    
    protected $_userLastName;
    
    protected $_userNewsletter;
    
    protected $_userId;
    
    protected $_result;
    
    public function _construct() 
    {
        parent::_construct();
    }
    
    protected function setEmail($email = '')
    {
        if (!Zend_Validate::is($email, 'EmailAddress'))
        {
            $this->_result = 'wrongemail';
        }
        else
        {
            $this->_userEmail = $email;
        }
    }
    
    protected function setSinglePassword($password){
        $sanitizedPassword = str_replace(array('\'', '%', '\\', '/', ' '), '', $password);
        
        if (strlen($sanitizedPassword) > 16 || $sanitizedPassword != trim($password))
        {
            $this->_result = 'wrongemail';
        }
        
        $this->_userPassword = $sanitizedPassword;
    }
    
    protected function setPassword($password = '', $confirmation = '')
    {        
        $sanitizedPassword = str_replace(array('\'', '%', '\\', '/', ' '), '', $password);
        
        if ($password != $sanitizedPassword)
        {
            $this->_result = 'dirtypassword,';
            return true;
        }
        
        if (strlen($sanitizedPassword) < 6)
        {
            $this->_result = 'shortpassword,';
            return true;
        }
        
        if (strlen($sanitizedPassword) > 16)
        {
            $this->_result = 'longpassword,';
            return true;
        }
        
        if ($sanitizedPassword != $confirmation)
        {
            $this->_result = 'notsamepasswords,';
            return true;
        }
        
        $this->_userPassword = $sanitizedPassword;
    }
    
    protected function setName($firstname = '', $lastname = '')
    {
        $firstname = trim($firstname);
        $lastname = trim($lastname);
        
        $stop = false;
        
        if ($firstname == '')
        {
            $this->_result .= 'nofirstname,';
            $stop = true;
        }
        
        if ($lastname == '')
        {
            $this->_result .= 'nolastname,';
            $stop = true;
        }
        
        if ($stop == true)
        {
            return true;
        }
        
        $sanitizedFname = str_replace(array('\'', '%', '\\', '/'), '', $firstname);
        
        if ($sanitizedFname != $firstname)
        {
            $this->_result .= 'dirtyfirstname,';
            $stop = true;
        }
        
        $sanitizedLname = str_replace(array('\'', '%', '\\', '/'), '', $lastname);
        
        if ($sanitizedLname != $lastname)
        {
            $this->_result .= 'dirtylastname,';
            $stop = true;
        }
        
        if ($stop != true)
        {
            $this->_userFirstName = $firstname;
            $this->_userLastName = $lastname;
        }
    }
    
    protected function setNewsletter($newsletter = 'no')
    {
        if ($newsletter == 'ok')
        {
            $this->_userNewsletter = true;
        }
        else
        {
            $this->_userNewsletter = false;
        }
    }
    
    protected function isEmailExist()
    {
        $customer = Mage::getModel('customer/customer');
        
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($this->_userEmail);
        
        if($customer->getId())
        {
            return true;
        }

        return false;
    }
    
    public function getResult()
    {
        return $this->_result;
    }
}
?>