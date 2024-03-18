<?php

class Webskitters_Creditsave_Model_Creditsave extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('creditsave/creditsave');
    }
}