<?php

class Webskitters_Creditsave_Model_Mysql4_Creditsave_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('creditsave/creditsave');
    }
}