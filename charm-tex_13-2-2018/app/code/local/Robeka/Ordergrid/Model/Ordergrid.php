<?php

class Robeka_Ordergrid_Model_Ordergrid extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ordergrid/ordergrid');
    }
}