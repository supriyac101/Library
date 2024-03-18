<?php

class Potato_PrintAnyOrder_Block_Adminhtml_System_Config_Form_Field_File
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element
            ->setValue(null)
        ;
        return parent::render($element);
    }
}