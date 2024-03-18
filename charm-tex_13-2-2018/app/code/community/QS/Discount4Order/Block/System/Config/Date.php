<?php

/**
 * Created by PhpStorm.
 * User: inknex
 * Date: 09/06/2015
 * Time: 3:30 PM
 */
class QS_Discount4Order_Block_System_Config_Date extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setFormat(Varien_Date::DATE_INTERNAL_FORMAT);
        $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
        return parent::render($element);
    }
}