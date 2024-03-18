<?php
class Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Edit_Tab_Category_Element_Image extends Varien_Data_Form_Element_Abstract {
	
	public function getElementHtml()
    {
        return '<div style="overflow:auto; max-height: 400px; max-width: 600px;" id="' . $this->getId() . '">' . $this->getValue() . '</div>';;
    }
}
