<?php

class Webskitters_Creditsave_Model_Mysql4_Creditsave extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the creditsave_id refers to the key field in your database table.
        $this->_init('creditsave/creditsave', 'creditsave_id');
    }
}