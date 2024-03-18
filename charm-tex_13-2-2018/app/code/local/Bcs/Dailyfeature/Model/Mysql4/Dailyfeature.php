<?php

class Bcs_Dailyfeature_Model_Mysql4_Dailyfeature extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the dailyfeature_id refers to the key field in your database table.
        $this->_init('dailyfeature/dailyfeature', 'dailyfeature_id');
    }
}