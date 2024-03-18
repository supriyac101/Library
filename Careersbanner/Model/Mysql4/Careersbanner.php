<?php

class Custom_Careersbanner_Model_Mysql4_Careersbanner extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the careersbanner_id refers to the key field in your database table.
        $this->_init('careersbanner/careersbanner', 'careersbanner_id');
    }
}