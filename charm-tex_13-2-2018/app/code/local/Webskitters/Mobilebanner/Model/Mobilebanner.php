<?php

class Webskitters_Mobilebanner_Model_Mobilebanner extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mobilebanner/mobilebanner');
    }
}