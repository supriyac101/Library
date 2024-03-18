<?php

class Custom_Careers_Model_Mysql4_Careers extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the careers_id refers to the key field in your database table.
        $this->_init('careers/careers', 'careers_id');
    }
}