<?php

class Custom_Careersbanner_Model_Careersbanner extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('careersbanner/careersbanner');
    }
}