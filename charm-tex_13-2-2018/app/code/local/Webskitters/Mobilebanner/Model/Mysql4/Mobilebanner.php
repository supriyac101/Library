<?php

class Webskitters_Mobilebanner_Model_Mysql4_Mobilebanner extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the mobilebanner_id refers to the key field in your database table.
        $this->_init('mobilebanner/mobilebanner', 'mobilebanner_id');
    }
}