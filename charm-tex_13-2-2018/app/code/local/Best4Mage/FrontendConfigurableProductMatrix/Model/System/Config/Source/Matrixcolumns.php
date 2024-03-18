<?php

class Best4Mage_FrontendConfigurableProductMatrix_Model_System_Config_Source_Matrixcolumns extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    { 
		return array(
			array('value' => '', 'label'=>Mage::helper('adminhtml')->__('No Selection')),
			array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Name')),
			array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('SKU')),
			array('value' => 3, 'label'=>Mage::helper('adminhtml')->__('Weight'))
        );
	}
	
	public function toOptionArray()
    {
        return $this->getAllOptions();
    }

}