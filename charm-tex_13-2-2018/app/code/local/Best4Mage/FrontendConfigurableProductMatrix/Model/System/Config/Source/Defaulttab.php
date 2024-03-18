<?php

class Best4Mage_FrontendConfigurableProductMatrix_Model_System_Config_Source_Defaulttab extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
		return array(
			array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Order One')),
			array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Order Multiple'))
        );
	}
	
	public function toOptionArray()
    {
        return $this->getAllOptions();
    }

}