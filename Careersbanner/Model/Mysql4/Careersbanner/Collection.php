<?php

class Custom_Careersbanner_Model_Mysql4_Careersbanner_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('careersbanner/careersbanner');
    }
}