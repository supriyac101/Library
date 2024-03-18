<?php

class Extendware_EWPRibbon_Block_Adminhtml_Image_Edit extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_removeButton('reset');
        $this->_removeButton('saveandreload');
        if ($this->getImage()->getNamespace() == 'default') {
        	$this->_removeButton('delete');
        }
    }

    public function getHeaderText()
    {
        return $this->__('Ribbon Image');
    }
    
	public function getImage() {
        return Mage::registry('ew:current_image');
    }
}
