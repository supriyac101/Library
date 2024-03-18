<?php

class Robeka_Ordergrid_Model_Mysql4_Ordergrid extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the ordergrid_id refers to the key field in your database table.
        $this->_init('ordergrid/ordergrid', 'ordergrid_id');
    }
}